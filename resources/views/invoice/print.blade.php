<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $order->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            background: #eee;
        }

        .actions {
            width: 210mm;
            margin: 16px auto 0;
            display: flex;
            gap: 8px;
        }

        .actions a {
            flex: 1;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            padding: 10px 0;
            border: 1px solid #111;
            border-radius: 6px;
            background: #fff;
            color: #111;
        }

        .actions a.primary {
            background: #111;
            color: #fff;
        }

        .sheet {
            width: 210mm;
            min-height: 290mm;
            margin: 12px auto 24px;
            background: #fff;
            padding: 15mm;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .15);
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #111;
            padding-bottom: 12px;
        }

        .inv {
            font-size: 26px;
            font-weight: bold;
            letter-spacing: 3px;
        }

        .num {
            font-size: 14px;
            margin-top: 2px;
        }

        .meta {
            text-align: right;
            font-size: 12px;
            line-height: 1.7;
        }

        .status {
            display: inline-block;
            border: 2px solid #111;
            border-radius: 4px;
            padding: 3px 12px;
            font-weight: bold;
            font-size: 12px;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .parties {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin: 20px 0;
            font-size: 12px;
            line-height: 1.7;
        }

        .parties h3 {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #777;
            margin-bottom: 4px;
        }

        .parties b.name {
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            padding: 8px 6px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        th {
            background: #f3f3f3;
            border-top: 2px solid #111;
            border-bottom: 2px solid #111;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1px;
        }

        td.r,
        th.r {
            text-align: right;
        }

        td.c,
        th.c {
            text-align: center;
        }

        tfoot th {
            border-bottom: 2px solid #111;
            background: #fff;
            font-size: 13px;
            text-transform: none;
            letter-spacing: 0;
        }

        .note {
            font-size: 12px;
            margin-top: 16px;
            padding: 8px 10px;
            border: 1px dashed #aaa;
        }

        .sign {
            margin-top: 48px;
            text-align: right;
            font-size: 12px;
        }

        .sign b {
            display: inline-block;
            margin-top: 56px;
            border-top: 1px solid #111;
            padding-top: 4px;
            min-width: 220px;
        }

        @media print {
            body {
                background: #fff;
            }

            .actions {
                display: none;
            }

            .sheet {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 10mm;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="sheet">
        <div class="top">
            <div>
                <p class="inv">INVOICE</p>
                <p class="num">{{ $order->invoice_number }}</p>
            </div>
            <div class="meta">
                <p>Dibuat: {{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                <p>Tanggal Kirim: <b>{{ $order->delivery_date->translatedFormat('d M Y') }}</b></p>
                <span class="status">{{ \App\Models\Order::STATUS[$order->status] ?? $order->status }}</span>
            </div>
        </div>

        <div class="parties">
            <div>
                <h3>Dari (Katering)</h3>
                <b class="name">{{ $order->merchant->company_name }}</b>
                <p>{{ $order->merchant->address }}</p>
                <p>{{ $order->merchant->city }}{{ $order->merchant->phone ? ' — '.$order->merchant->phone : '' }}</p>
            </div>
            <div style="text-align:right">
                <h3>Untuk (Kantor)</h3>
                <b class="name">{{ $order->customer->name }}</b>
                <p>{{ $order->customer->address ?: $order->customer->email }}</p>
                @if ($order->customer->phone)
                    <p>{{ $order->customer->phone }}</p>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:36px" class="c">No</th>
                    <th>Menu</th>
                    <th class="r">Harga</th>
                    <th class="c" style="width:70px">Porsi</th>
                    <th class="r" style="width:120px">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $i => $item)
                    <tr>
                        <td class="c">{{ $i + 1 }}</td>
                        <td>{{ $item->menu_name }}</td>
                        <td class="r">{{ 'Rp'.number_format($item->price, 0, ',', '.') }}</td>
                        <td class="c">{{ $item->portions }}</td>
                        <td class="r">{{ 'Rp'.number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="r">TOTAL</th>
                    <th class="r">{{ 'Rp'.number_format($order->total_price, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        @if ($order->note)
            <p class="note"><b>Catatan:</b> {{ $order->note }}</p>
        @endif

        <div class="sign">
            Hormat kami,<br>
            <b>{{ $order->merchant->company_name }}</b>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => setTimeout(() => window.print(), 400));
    </script>
</body>

</html>
