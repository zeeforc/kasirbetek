<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Laporan Order</title>
    <style>
        body {
            margin: 0 auto;
            font-family: Arial, sans-serif;
            background: #FFFFFF;
            font-size: 12px;
            color: #001028;
        }

        header {
            padding: 10px 0;
            text-align: center;
            border-bottom: 1px solid #5D6975;
            margin-bottom: 20px;
        }

        #logo img {
            width: 120px;
        }

        h1 {
            font-size: 2em;
            margin: 14px 0;
        }

        span {
            font-size: 14px;
            color: #5D6975;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #C1CED9;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #F5F5F5;
            color: #5D6975;
        }

        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 30px;
            border-top: 1px solid #C1CED9;
            text-align: center;
            padding: 8px 0;
            font-size: 0.8em;
            color: #5D6975;
        }
    </style>
</head>

<body>

    <header>
        <div id="logo">
            @php
                $logoPath = $logo ? storage_path('app/public/' . $logo) : null;
            @endphp

            @if ($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="Logo">
            @endif
        </div>
        <h1>Laporan Penjualan<br><span>{{ '(' . $fileName . ')' }}</span></h1>
    </header>

    <main>
        @php
            $total_Order_amount = 0;
            $total_Profit_amount = 0;
            $total_Rental_amount = 0;
        @endphp

        @foreach($data as $order)
            @php
                $productItems = $order->transactionItems?->whereNotNull('product_id') ?? collect();
                $rentalItems = $order->rentals ?? collect();
                $total_profit_amount = 0;
                $total_rental_amount_per_order = 0;
            @endphp

            <table>
                <thead>
                    <tr>
                        <th colspan="4" style="background-color:yellow; color:black;">
                            No.Transaksi: {{ $order->transaction_number }}
                        </th>
                        <th colspan="2" style="background-color:yellow; color:black;">
                            Pembayaran: {{ $order->paymentMethod?->name ?? 'Tidak diketahui' }}
                        </th>
                    </tr>

                    {{-- Header untuk produk --}}
                    @if ($productItems->count())
                        <tr>
                            <th>Produk</th>
                            <th>Harga Modal</th>
                            <th>Harga Jual</th>
                            <th>Qty</th>
                            <th>Total Bayar</th>
                            <th>Total Profit</th>
                        </tr>
                    @endif
                </thead>

                <tbody>
                    {{-- Baris produk --}}
                    @foreach($productItems as $item)
                        <tr>
                            <td>{{ $item->productWithTrashed?->name ?? 'Produk tidak ditemukan' }}</td>
                            <td>Rp {{ number_format($item->cost_price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->total_profit, 0, ',', '.') }}</td>
                        </tr>
                        @php $total_profit_amount += (int) $item->total_profit; @endphp
                    @endforeach

                    {{-- Jika ada rental, tambahkan header rental di tengah table --}}
                    @if ($rentalItems->count())
                        <tr>
                            <th>Rental</th>
                            <th>Harga Sewa</th>
                            <th>Durasi</th>
                            <th>Qty</th>
                            <th>Total Bayar</th>
                            <th>Keterangan</th>
                        </tr>

                        @foreach($rentalItems as $r)
                            @php
                                $dur = (int) ($r->duration ?? 1);
                                $qty = (int) ($r->quantity ?? 1);
                                $price = (int) ($r->price ?? 0);
                                $sub = $price * $qty * $dur;
                                $total_rental_amount_per_order += $sub;
                            @endphp

                            <tr>
                                <td>{{ $r->rentalItem?->name ?? 'Rental item' }}</td>
                                <td>Rp {{ number_format($price, 0, ',', '.') }}</td>
                                <td>{{ $dur }} hari</td>
                                <td>{{ $qty }}</td>
                                <td>Rp {{ number_format($sub, 0, ',', '.') }}</td>
                                <td>{{ $r->type }}</td>
                            </tr>
                        @endforeach
                    @endif

                    {{-- Ringkasan per transaksi --}}
                    <tr>
                        <td colspan="4">Total</td>
                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td>
                            @if ($productItems->count())
                                Rp {{ number_format($total_profit_amount, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>

                    {{-- Jika ada rental, tampilkan total rental per transaksi (opsional, tapi membantu) --}}
                    @if ($rentalItems->count())
                        <tr>
                            <td colspan="4" style="text-align:right;">Total Rental</td>
                            <td colspan="2">Rp {{ number_format($total_rental_amount_per_order, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @php
                $total_Order_amount += (int) $order->total;
                $total_Profit_amount += (int) $total_profit_amount;
                $total_Rental_amount += (int) $total_rental_amount_per_order;
            @endphp
        @endforeach

        <table>
            <thead>
                <tr>
                    <th style="background-color:white; color:black; font-size:16px">
                        Total Uang Masuk: Rp {{ number_format($total_Order_amount, 0, ',', '.') }}
                    </th>
                    <th style="background-color:white; color:black; font-size:16px">
                        Total Keuntungan Produk: Rp {{ number_format($total_Profit_amount, 0, ',', '.') }}
                    </th>
                </tr>
                <tr>
                    <th colspan="2" style="background-color:white; color:black; font-size:16px">
                        Total Rental: Rp {{ number_format($total_Rental_amount, 0, ',', '.') }}
                    </th>
                </tr>
            </thead>
        </table>
    </main>

    <footer>
        Laporan ini dihasilkan secara otomatis tanpa tanda tangan.
    </footer>

</body>

</html>
