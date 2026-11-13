<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }} — RHS HERITAGE</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #1E3A2F;
            --primary-dark: #12241d;
            --primary-light: #2A5243;
            --gold: #C5A028;
            --gold-light: #F4E8C1;
            --neutral-900: #0F172A;
            --neutral-800: #1E293B;
            --neutral-700: #334155;
            --neutral-600: #475569;
            --neutral-500: #64748B;
            --neutral-400: #94A3B8;
            --neutral-300: #CBD5E1;
            --neutral-200: #E2E8F0;
            --neutral-100: #F1F5F9;
            --neutral-50: #F8FAFC;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #E2E8F0;
            color: var(--neutral-800);
            font-size: 13px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Action Bar (Screen Only) */
        .action-bar {
            background: #ffffff;
            border-bottom: 1px solid var(--neutral-300);
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .action-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .action-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--neutral-900);
        }

        .action-subtitle {
            font-size: 12px;
            color: var(--neutral-500);
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .btn-print {
            background-color: var(--primary);
            color: #ffffff;
        }
        .btn-print:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: #ffffff;
            color: var(--neutral-700);
            border-color: var(--neutral-300);
        }
        .btn-secondary:hover {
            background-color: var(--neutral-100);
        }

        /* Printable Paper Canvas */
        .invoice-canvas-wrapper {
            padding: 32px 16px;
            display: flex;
            justify-content: center;
        }

        .invoice-card {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            margin: 0 auto;
            padding: 36px 42px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Top Accent Stripes */
        .top-accent-bar {
            height: 6px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 65%, var(--gold) 100%);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            border-radius: 4px 4px 0 0;
        }

        /* Header Section */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--neutral-100);
        }

        .brand-title {
            font-family: 'Cinzel', serif;
            font-size: 24px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 1.5px;
            line-height: 1.2;
        }

        .brand-subtitle {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--gold);
            margin-top: 2px;
        }

        .brand-address {
            font-size: 11px;
            color: var(--neutral-600);
            line-height: 1.45;
            margin-top: 6px;
        }

        .invoice-title {
            text-align: right;
        }

        .doc-label {
            font-family: 'Cinzel', serif;
            font-size: 26px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 1px;
        }

        .doc-number {
            font-size: 13px;
            font-weight: 700;
            font-family: monospace;
            color: var(--neutral-800);
            margin-top: 4px;
        }

        .doc-meta {
            font-size: 11px;
            color: var(--neutral-500);
            margin-top: 3px;
        }

        /* Status Badge */
        .badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 6px;
        }

        .badge-success {
            background-color: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }
        .badge-warning {
            background-color: #FFFBEB;
            color: #92400E;
            border: 1px solid #FDE68A;
        }
        .badge-danger {
            background-color: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }
        .badge-info {
            background-color: #EFF6FF;
            color: #1E40AF;
            border: 1px solid #BFDBFE;
        }

        /* 2-Column Info Grid: Billed To & Shipped To */
        .grid-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }

        .info-card {
            background-color: var(--neutral-50);
            border: 1px solid var(--neutral-200);
            border-radius: 8px;
            padding: 14px 16px;
        }

        .info-header {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--primary);
            margin-bottom: 8px;
            border-bottom: 1px dashed var(--neutral-300);
            padding-bottom: 6px;
        }

        .info-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--neutral-900);
        }

        .info-detail {
            font-size: 11.5px;
            color: var(--neutral-600);
            line-height: 1.45;
            margin-top: 4px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background-color: var(--primary);
            color: #ffffff;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 10px 12px;
            border: none;
        }

        .items-table th:first-child {
            border-radius: 6px 0 0 0;
            text-align: center;
        }
        .items-table th:last-child {
            border-radius: 0 6px 0 0;
            text-align: right;
        }

        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--neutral-200);
            font-size: 12px;
            color: var(--neutral-700);
            vertical-align: middle;
        }

        .items-table tr:nth-child(even) td {
            background-color: #FAFAFA;
        }

        .item-product-name {
            font-weight: 700;
            color: var(--neutral-900);
            font-size: 12.5px;
        }

        .item-variant {
            font-size: 11px;
            color: var(--neutral-500);
            margin-top: 2px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }

        /* Calculation & Summary Section */
        .summary-wrapper {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        /* Payment Details Box (Left) */
        .payment-box {
            background-color: var(--neutral-50);
            border: 1px solid var(--neutral-200);
            border-radius: 8px;
            padding: 14px 16px;
            font-size: 11.5px;
        }

        .payment-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .bank-item {
            margin-bottom: 6px;
            color: var(--neutral-700);
        }

        .terbilang-box {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed var(--neutral-300);
            font-style: italic;
            color: var(--neutral-600);
            font-size: 11px;
            line-height: 1.4;
        }

        /* Financial Breakdown (Right) */
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
        }

        .breakdown-table td {
            padding: 6px 0;
            font-size: 12px;
            color: var(--neutral-600);
        }

        .breakdown-table td.amount {
            text-align: right;
            font-weight: 600;
            color: var(--neutral-800);
            font-family: monospace;
        }

        .breakdown-total td {
            border-top: 2px solid var(--neutral-300);
            padding-top: 10px;
            font-size: 14px;
            font-weight: 800;
            color: var(--primary);
        }

        .breakdown-total td.amount {
            font-size: 16px;
            color: var(--primary);
            font-weight: 800;
        }

        /* Terms & Signatures */
        .footer-section {
            border-top: 1px solid var(--neutral-200);
            padding-top: 16px;
            margin-top: auto;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 20px;
            align-items: flex-end;
        }

        .policy-note {
            font-size: 10px;
            color: var(--neutral-500);
            line-height: 1.45;
        }

        .signature-box {
            text-align: center;
            font-size: 11px;
            color: var(--neutral-600);
        }

        .stamp-seal {
            margin: 6px auto;
            width: 92px;
            height: 92px;
            border: 2px dashed var(--gold);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-weight: 800;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            transform: rotate(-5deg);
            opacity: 0.9;
        }

        .signature-name {
            font-weight: 700;
            color: var(--neutral-900);
            border-top: 1px solid var(--neutral-400);
            padding-top: 4px;
            display: inline-block;
            min-width: 140px;
        }

        .brand-gratitude {
            text-align: center;
            font-size: 10.5px;
            color: var(--neutral-400);
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid var(--neutral-100);
            font-style: italic;
        }

        /* PRINT STYLING & MEDIA QUERIES */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 11.5px !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .action-bar {
                display: none !important;
            }

            .invoice-canvas-wrapper {
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
            }

            .invoice-card {
                width: 100% !important;
                max-width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }

            .top-accent-bar {
                position: static !important;
                margin-bottom: 12px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .items-table th {
                background-color: #1E3A2F !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .info-card, .payment-box {
                background-color: #F8FAFC !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .badge {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                size: A4 portrait;
                margin: 12mm 15mm 12mm 15mm;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar (Hanya tampil di layar/browser, otomatis tersembunyi saat Cetak/PDF) -->
    <div class="action-bar no-print">
        <div class="action-left">
            <div>
                <div class="action-title">
                    Pratinjau Dokumen Invoice Resmi RHS Heritage
                </div>
                <div class="action-subtitle">
                    Format resmi standar A4 &bull; Bebas watermark &bull; Siap cetak fisik maupun Simpan ke PDF
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <button onclick="window.print()" class="action-btn btn-print">
                <span>Cetak / Simpan PDF</span>
            </button>
            <button onclick="window.close()" class="action-btn btn-secondary">
                <span>Tutup</span>
            </button>
        </div>
    </div>

    <!-- Paper Sheet Canvas Container -->
    <div class="invoice-canvas-wrapper">
        <div class="invoice-card">
            <div class="top-accent-bar"></div>

            @php
                $storeInfo = $storeInfo ?? \App\Models\User::getAdminStoreInfo();
            @endphp

            <div>
                <!-- Header: Identitas Brand & Invoice Meta -->
                <table class="header-table">
                    <tr>
                        <td style="vertical-align: top; width: 58%;">
                            <div class="brand-title">
                                RHS HERITAGE
                            </div>
                            <div class="brand-subtitle">
                                Warisan Busana & Kerajinan Batik Nusantara
                            </div>
                            <div class="brand-address">
                                {{ $storeInfo['address'] }}<br>
                                WhatsApp: <strong>{{ $storeInfo['phone'] }}</strong> &bull; Email: {{ $storeInfo['email'] }}<br>
                                Website: <strong>{{ $storeInfo['website'] ?? 'rhsheritage.my.id' }}</strong>
                            </div>
                        </td>
                        <td style="vertical-align: top; width: 42%;" class="invoice-title">
                            <div class="doc-label">INVOICE</div>
                            <div class="doc-number">#{{ $order->order_number }}</div>
                            <div class="doc-meta">
                                Tanggal: <strong>{{ $order->created_at ? $order->created_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</strong><br>
                                Pukul: {{ $order->created_at ? $order->created_at->format('H:i') : now()->format('H:i') }} WIB
                            </div>
                            
                            @php
                                $statusEnum = $order->status instanceof \App\Enums\OrderStatus
                                    ? $order->status
                                    : (is_string($order->status) ? \App\Enums\OrderStatus::tryFrom($order->status) : null);
                            @endphp

                            @if($statusEnum === \App\Enums\OrderStatus::Selesai)
                                <span class="badge badge-success">
                                    Transaksi Selesai
                                </span>
                            @elseif($statusEnum === \App\Enums\OrderStatus::Dikirim)
                                <span class="badge badge-info">
                                    Sedang Dikirim
                                </span>
                            @elseif($statusEnum === \App\Enums\OrderStatus::Diproses)
                                <span class="badge badge-info">
                                    Sedang Diproses
                                </span>
                            @elseif($statusEnum === \App\Enums\OrderStatus::Dibatalkan)
                                <span class="badge badge-danger">
                                    Dibatalkan
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    Menunggu Konfirmasi
                                </span>
                            @endif
                        </td>
                    </tr>
                </table>

                <!-- Customer Information & Shipping Destination -->
                <div class="grid-info">
                    <div class="info-card">
                        <div class="info-header">
                            Ditagihkan Kepada (Customer)
                        </div>
                        <div class="info-name">{{ $order->customer_name }}</div>
                        <div class="info-detail">
                            <strong>No. WhatsApp / HP:</strong> {{ $order->customer_phone }}<br>
                            <strong>Email:</strong> {{ $order->customer_email ?? $order->user?->email ?? '-' }}<br>
                            <strong>Status Akun:</strong> {{ $order->user ? 'Pelanggan Terdaftar' : 'Guest / Tamu' }}
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-header">
                            Tujuan Pengiriman (Shipment)
                        </div>
                        <div class="info-name">{{ $order->customer_name }}</div>
                        <div class="info-detail">
                            {{ $order->address_line }}<br>
                            {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}<br>
                            <strong>Zona:</strong> {{ $order->shipping_zone_name ?? $order->shippingZone?->name ?? 'Reguler' }}
                            @if($order->tracking_number)
                                <br><strong>No. Resi J&T:</strong> <span class="font-mono" style="font-weight: 700; color: var(--primary);">{{ $order->tracking_number }}</span>
                            @endif
                            @if($order->note)
                                <br><strong>Catatan:</strong> <em style="color: var(--neutral-500);">{{ $order->note }}</em>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Product Items Table -->
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 45%; text-align: left;">Deskripsi Produk & Varian</th>
                            <th style="width: 12%; text-align: center;">Berat</th>
                            <th style="width: 10%; text-align: center;">Jumlah</th>
                            <th style="width: 14%; text-align: right;">Harga Satuan</th>
                            <th style="width: 14%; text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->orderItems as $index => $item)
                            <tr>
                                <td class="text-center font-mono">{{ $index + 1 }}</td>
                                <td>
                                    <div class="item-product-name">{{ $item->product_name }}</div>
                                    <div class="item-variant">Varian: {{ $item->variant_label }}</div>
                                </td>
                                <td class="text-center font-mono text-neutral-500">
                                    {{ $item->weight_gram ? number_format($item->weight_gram, 0, ',', '.') . ' gr' : '-' }}
                                </td>
                                <td class="text-center font-mono" style="font-weight: 600;">
                                    {{ $item->quantity }}
                                </td>
                                <td class="text-right font-mono">
                                    Rp{{ number_format((float) $item->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="text-right font-mono" style="font-weight: 700; color: var(--neutral-900);">
                                    Rp{{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center" style="padding: 24px; color: var(--neutral-400);">
                                    Tidak ada item produk terlampir.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Summary & Payment Information -->
                <div class="summary-wrapper">
                    <!-- Left: Payment Info & Terbilang -->
                    <div class="payment-box">
                        <div class="payment-title">
                            Rekening Resmi Pembayaran RHS Heritage
                        </div>
                        <div class="bank-item">
                            &bull; <strong>Bank Central Asia (BCA):</strong> <span class="font-mono">015-888-2999</span> a.n. RHS Heritage Official
                        </div>
                        <div class="bank-item">
                            &bull; <strong>Bank Mandiri:</strong> <span class="font-mono">138-00-1928-3333</span> a.n. RHS Heritage Official
                        </div>
                        <div class="bank-item">
                            &bull; <strong>Bank BRI:</strong> <span class="font-mono">0021-01-002938-50-1</span> a.n. RHS Heritage Official
                        </div>

                        <div class="terbilang-box">
                            <strong>Terbilang:</strong><br>
                            # {{ \App\Support\Terbilang::rupiah($order->total) }} #
                        </div>
                    </div>

                    <!-- Right: Cost Calculation -->
                    <div>
                        <table class="breakdown-table">
                            <tr>
                                <td>Subtotal Produk</td>
                                <td class="amount">Rp{{ number_format((float) $order->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Total Berat Paket</td>
                                <td class="amount">{{ number_format((int) $order->total_weight_gram, 0, ',', '.') }} gram</td>
                            </tr>
                            <tr>
                                <td>Ongkos Kirim ({{ $order->shipping_zone_name ?? $order->shippingZone?->name ?? 'Pengiriman' }})</td>
                                <td class="amount">Rp{{ number_format((float) $order->shipping_cost, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Potongan / Diskon Promo</td>
                                <td class="amount" style="color: #059669;">- Rp0</td>
                            </tr>
                            <tr class="breakdown-total">
                                <td>TOTAL PEMBAYARAN</td>
                                <td class="amount">Rp{{ number_format((float) $order->total, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer: Signatures & Authentic Notes -->
            <div class="footer-section">
                <div class="footer-grid">
                    <div class="policy-note">
                        <strong>Syarat & Ketentuan Transaksi:</strong>
                        <ol style="margin-left: 14px; margin-top: 4px; line-height: 1.4;">
                            <li>Dokumen ini merupakan bukti transaksi sah yang diterbitkan secara elektronik oleh sistem RHS Heritage.</li>
                            <li>Klaim penukaran atau retur barang diterima maksimal 2x24 jam sejak barang diterima sesuai kebijakan Return Management System (RMS) RHS Heritage.</li>
                            <li>Lacak pembaruan status pesanan sewaktu-waktu di: <strong>{{ $storeInfo['website'] ?? 'rhsheritage.my.id' }}/lacak-pesanan</strong>.</li>
                        </ol>
                    </div>

                    <div class="signature-box">
                        <div>{{ $storeInfo['city'] ?? 'Surakarta' }}, {{ $order->created_at ? $order->created_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</div>
                        <div class="stamp-seal">
                            <span>RHS HERITAGE</span>
                            <span style="font-size: 7.5px; color: var(--gold); margin-top: 2px;">VERIFIED</span>
                        </div>
                        <div class="signature-name">{{ $storeInfo['name'] }}</div>
                        <div style="font-size: 9.5px; color: var(--neutral-400);">Otorisasi Dokumen Resmi</div>
                    </div>
                </div>

                <div class="brand-gratitude">
                    "Terima kasih telah mempercayakan koleksi adibusana dan kerajinan budaya nusantara kepada RHS Heritage."
                </div>
            </div>
        </div>
    </div>

    <!-- Script auto-print jika parameter ?print=1 disertakan -->
    <script>
        if (window.location.search.includes('print=1')) {
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 350);
            });
        }
    </script>
</body>
</html>
