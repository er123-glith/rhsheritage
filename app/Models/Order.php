<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'address_line',
        'city',
        'province',
        'postal_code',
        'note',
        'shipping_zone_id',
        'shipping_zone_name',
        'total_weight_gram',
        'subtotal',
        'shipping_cost',
        'total',
        'status',
        'cancellation_reason',
        'tracking_number',
        'courier_name',
        'shipping_date',
        'shipping_proof_path',
        'shipping_note',
        'delivered_at',
        'delivery_proof_path',
        'delivery_confirmed_by',
        'payload_json',
        'sent_to_wa_at',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload_json'      => 'array',
            'status'            => OrderStatus::class,
            'sent_to_wa_at'     => 'datetime',
            'confirmed_at'      => 'datetime',
            'shipping_date'     => 'datetime',
            'delivered_at'      => 'datetime',
            'subtotal'          => 'decimal:2',
            'shipping_cost'     => 'decimal:2',
            'total'             => 'decimal:2',
            'total_weight_gram' => 'integer',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class)->orderBy('created_at', 'asc')->orderBy('id', 'asc');
    }

    public function isCancelled(): bool
    {
        $status = $this->status instanceof OrderStatus
            ? $this->status
            : (is_string($this->status) ? OrderStatus::tryFrom($this->status) : null);

        return $status === OrderStatus::Dibatalkan;
    }

    public function activeReturn(): ?OrderReturn
    {
        $returns = $this->relationLoaded('returns') ? $this->returns : $this->returns()->get();

        return $returns->first(function ($r) {
            $val = $r->status instanceof \App\Enums\ReturnStatus ? $r->status->value : (string) $r->status;
            return ! in_array($val, [
                \App\Enums\ReturnStatus::Ditolak->value,
                \App\Enums\ReturnStatus::Selesai->value,
            ], true);
        });
    }

    /**
     * Membangun representasi alur 4 tahapan pesanan (Pipeline RHS Heritage):
     * 1. Menunggu Konfirmasi
     * 2. Diproses
     * 3. Dikirim
     * 4. Selesai
     * Serta menangani cabang Dibatalkan secara terpisah dan terverifikasi.
     *
     * @return array{steps: array, cancellation: ?array, has_inconsistency: bool, is_cancelled: bool}
     */
    public function getTimelineProgress(): array
    {
        $currentStatus = $this->status instanceof OrderStatus
            ? $this->status
            : (is_string($this->status) ? OrderStatus::tryFrom($this->status) : null);

        $isCancelled = ($currentStatus === OrderStatus::Dibatalkan);

        $logs = $this->relationLoaded('statusLogs') ? $this->statusLogs : $this->statusLogs()->get();

        foreach ($logs as $l) {
            if (! $l->relationLoaded('order')) {
                $l->setRelation('order', $this);
            }
        }

        // Cari log untuk masing-masing status
        $logMenunggu = $logs->first(function ($l) {
            $to = $l->to_status instanceof OrderStatus ? $l->to_status->value : (string) $l->to_status;
            return $to === OrderStatus::MenungguKonfirmasi->value;
        });

        $logDiproses = $logs->first(function ($l) {
            $to = $l->to_status instanceof OrderStatus ? $l->to_status->value : (string) $l->to_status;
            return $to === OrderStatus::Diproses->value;
        });

        $logDikirim = $logs->first(function ($l) {
            $to = $l->to_status instanceof OrderStatus ? $l->to_status->value : (string) $l->to_status;
            return $to === OrderStatus::Dikirim->value;
        });

        $logSelesai = $logs->first(function ($l) {
            $to = $l->to_status instanceof OrderStatus ? $l->to_status->value : (string) $l->to_status;
            return $to === OrderStatus::Selesai->value;
        });

        $logBatal = $logs->first(function ($l) {
            $to = $l->to_status instanceof OrderStatus ? $l->to_status->value : (string) $l->to_status;
            return $to === OrderStatus::Dibatalkan->value;
        });

        $hasInconsistency = false;

        // Step 1: Menunggu Konfirmasi
        $step1Time = $logMenunggu?->created_at ?? $this->created_at;
        $step1State = 'completed';
        if ($currentStatus === OrderStatus::MenungguKonfirmasi) {
            $step1State = 'current';
        }
        $step1 = [
            'key'            => OrderStatus::MenungguKonfirmasi->value,
            'index'          => 1,
            'enum'           => OrderStatus::MenungguKonfirmasi,
            'label'          => OrderStatus::MenungguKonfirmasi->label(),
            'icon'           => OrderStatus::MenungguKonfirmasi->icon(),
            'description'    => OrderStatus::MenungguKonfirmasi->stepDescription(),
            'state'          => $step1State,
            'is_completed'   => $step1State === 'completed',
            'is_current'     => $step1State === 'current',
            'is_upcoming'    => false,
            'timestamp'      => $step1Time,
            'formatted_time' => $step1Time ? $step1Time->translatedFormat('d M Y, H:i') . ' WIB' : null,
            'note'           => $logMenunggu?->note ?? 'Pesanan berhasil dibuat di sistem.',
            'actor'          => $logMenunggu?->actor_label ?? 'Pelanggan (Checkout)',
        ];

        // Step 2: Diproses
        $step2Time = $this->confirmed_at ?? $logDiproses?->created_at;
        $hasDiprosesProof = ($logDiproses !== null)
            || ($this->confirmed_at !== null)
            || $logs->contains(function ($l) {
                $from = $l->from_status instanceof OrderStatus ? $l->from_status->value : (string) $l->from_status;
                return $from === OrderStatus::Diproses->value;
            });

        if ($isCancelled) {
            if ($hasDiprosesProof) {
                $step2State = 'completed';
            } else {
                $step2State = 'cancelled';
                $step2Time = null;
            }
        } elseif ($currentStatus === OrderStatus::MenungguKonfirmasi) {
            $step2State = 'upcoming';
            $step2Time = null;
        } elseif ($currentStatus === OrderStatus::Diproses) {
            $step2State = 'current';
        } else {
            // dikirim atau selesai
            if ($hasDiprosesProof) {
                $step2State = 'completed';
            } else {
                $step2State = 'skipped';
                $hasInconsistency = true;
            }
        }
        $step2 = [
            'key'            => OrderStatus::Diproses->value,
            'index'          => 2,
            'enum'           => OrderStatus::Diproses,
            'label'          => OrderStatus::Diproses->label(),
            'icon'           => OrderStatus::Diproses->icon(),
            'description'    => OrderStatus::Diproses->stepDescription(),
            'state'          => $step2State,
            'is_completed'   => $step2State === 'completed',
            'is_current'     => $step2State === 'current',
            'is_upcoming'    => $step2State === 'upcoming',
            'timestamp'      => $step2Time,
            'formatted_time' => $step2Time ? $step2Time->translatedFormat('d M Y, H:i') . ' WIB' : null,
            'note'           => $logDiproses?->note ?? ($step2State === 'skipped' ? 'Inkonsistensi: Tahapan tidak tercatat pada riwayat' : null),
            'actor'          => $logDiproses?->actor_label ?? ($step2Time ? 'Owner RHS Heritage' : null),
        ];

        // Step 3: Dikirim
        $step3Time = $this->shipping_date ?? $logDikirim?->created_at;
        $hasDikirimProof = ($logDikirim !== null)
            || ! empty($this->tracking_number)
            || ($this->shipping_date !== null)
            || $logs->contains(function ($l) {
                $from = $l->from_status instanceof OrderStatus ? $l->from_status->value : (string) $l->from_status;
                return $from === OrderStatus::Dikirim->value;
            });

        if ($isCancelled) {
            if ($hasDikirimProof) {
                $step3State = 'completed';
            } else {
                $step3State = 'cancelled';
                $step3Time = null;
            }
        } elseif (in_array($currentStatus, [OrderStatus::MenungguKonfirmasi, OrderStatus::Diproses], true)) {
            $step3State = 'upcoming';
            $step3Time = null;
        } elseif ($currentStatus === OrderStatus::Dikirim) {
            $step3State = 'current';
        } else {
            // selesai
            if ($hasDikirimProof) {
                $step3State = 'completed';
            } else {
                $step3State = 'skipped';
                $hasInconsistency = true;
            }
        }
        $dikirimNote = $logDikirim?->note;
        if (! $dikirimNote && $this->tracking_number) {
            $dikirimNote = "No. Resi J&T: {$this->tracking_number}";
        }
        $step3 = [
            'key'            => OrderStatus::Dikirim->value,
            'index'          => 3,
            'enum'           => OrderStatus::Dikirim,
            'label'          => OrderStatus::Dikirim->label(),
            'icon'           => OrderStatus::Dikirim->icon(),
            'description'    => OrderStatus::Dikirim->stepDescription(),
            'state'          => $step3State,
            'is_completed'   => $step3State === 'completed',
            'is_current'     => $step3State === 'current',
            'is_upcoming'    => $step3State === 'upcoming',
            'timestamp'      => $step3Time,
            'formatted_time' => $step3Time ? $step3Time->translatedFormat('d M Y, H:i') . ' WIB' : null,
            'note'           => $dikirimNote ?? ($step3State === 'skipped' ? 'Inkonsistensi: Tahapan tidak tercatat pada riwayat' : null),
            'actor'          => $logDikirim?->actor_label ?? ($step3Time ? 'Admin / Logistik RHS Heritage' : null),
        ];

        // Step 4: Selesai
        $step4Time = $this->delivered_at ?? $logSelesai?->created_at;
        if ($isCancelled) {
            $step4State = 'cancelled';
            $step4Time = null;
        } elseif ($currentStatus === OrderStatus::Selesai) {
            $step4State = 'completed';
        } else {
            $step4State = 'upcoming';
            $step4Time = null;
        }

        $step4Actor = null;
        if ($step4Time) {
            $confirmedByName = $this->relationLoaded('deliveryConfirmedBy')
                ? $this->deliveryConfirmedBy?->name
                : null;
            $step4Actor = $confirmedByName ? "Pelanggan ({$confirmedByName})" : 'Pelanggan (Konfirmasi Penerimaan)';
        }

        $step4 = [
            'key'            => OrderStatus::Selesai->value,
            'index'          => 4,
            'enum'           => OrderStatus::Selesai,
            'label'          => OrderStatus::Selesai->label(),
            'icon'           => OrderStatus::Selesai->icon(),
            'description'    => OrderStatus::Selesai->stepDescription(),
            'state'          => $step4State,
            'is_completed'   => $step4State === 'completed',
            'is_current'     => $currentStatus === OrderStatus::Selesai,
            'is_upcoming'    => $step4State === 'upcoming',
            'timestamp'      => $step4Time,
            'formatted_time' => $step4Time ? $step4Time->translatedFormat('d M Y, H:i') . ' WIB' : null,
            'note'           => $logSelesai?->note,
            'actor'          => $logSelesai?->actor_label ?? $step4Actor,
        ];

        // Metadata pembatalan
        $cancellation = null;
        if ($isCancelled) {
            $cancelTime = $logBatal?->created_at ?? $this->updated_at;
            $cancellation = [
                'is_cancelled'   => true,
                'reason'         => $this->cancellation_reason ?: ($logBatal?->note ?: 'Pesanan dibatalkan.'),
                'timestamp'      => $cancelTime,
                'formatted_time' => $cancelTime ? $cancelTime->translatedFormat('d M Y, H:i') . ' WIB' : null,
                'actor'          => $logBatal?->actor_label ?? 'Owner / Admin RHS Heritage',
            ];
        }

        return [
            'steps'             => [$step1, $step2, $step3, $step4],
            'cancellation'      => $cancellation,
            'has_inconsistency' => $hasInconsistency,
            'is_cancelled'      => $isCancelled,
        ];
    }

    public function shippingZone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function deliveryConfirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_confirmed_by')->withTrashed();
    }

    public function returns(): HasMany
    {
        return $this->hasMany(OrderReturn::class, 'order_id')->latest();
    }

    public function review(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Memeriksa apakah pesanan memiliki pengajuan retur aktif yang belum tuntas.
     */
    public function hasActiveReturn(): bool
    {
        return $this->returns()
            ->whereNotIn('status', [
                \App\Enums\ReturnStatus::Ditolak->value,
                \App\Enums\ReturnStatus::Selesai->value,
            ])
            ->exists();
    }

    public function getShippingAddressAttribute(): ?string
    {
        return $this->address_line;
    }

    public function getShippingCityAttribute(): ?string
    {
        return $this->city;
    }

    public function getCustomerEmailAttribute(): ?string
    {
        return $this->user?->email;
    }
}
