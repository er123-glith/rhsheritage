<?php

namespace App\Services;

use App\Models\ShippingZone;
use App\Support\Cart\CartItem;
use Illuminate\Support\Collection;

class ShippingCalculator
{
    /**
     * Hitung ongkos kirim dalam Rupiah (integer).
     *
     * Formula:
     *   billableKg = max(1, ceil(totalGram / 1000))
     *   cost       = base_rate + rate_per_extra_kg × (billableKg − 1)
     *
     * @param  Collection<CartItem>  $items
     */
    public function calculate(Collection $items, ShippingZone $zone): int
    {
        $totalGram   = $this->totalWeightGram($items);
        $billableKg  = max(1, (int) ceil($totalGram / 1000));
        $extraKg     = max(0, $billableKg - 1);

        return (int) ($zone->base_rate + $zone->rate_per_extra_kg * $extraKg);
    }

    /**
     * Total berat seluruh item dalam gram.
     *
     * @param  Collection<CartItem>  $items
     */
    public function totalWeightGram(Collection $items): int
    {
        return (int) $items->sum(fn(CartItem $item) => $item->totalWeightGram());
    }

    /**
     * Hitung ongkir hanya dari berat gram (tanpa Collection).
     */
    public function calculateFromGram(int $totalGram, ShippingZone $zone): int
    {
        $billableKg = max(1, (int) ceil($totalGram / 1000));
        $extraKg    = max(0, $billableKg - 1);

        return (int) ($zone->base_rate + $zone->rate_per_extra_kg * $extraKg);
    }
}
