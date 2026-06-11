<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1a1a1a;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .wrapper { padding: 40px 45px; }

        .header {
            border-bottom: 3px solid #f0c36d;
            padding-bottom: 18px;
            margin-bottom: 25px;
        }
        .brand {
            font-size: 26px;
            font-weight: bold;
            letter-spacing: 3px;
            color: #111;
        }
        .brand span { color: #f0c36d; }
        .brand-sub {
            font-size: 9px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #999;
            margin-top: 2px;
        }
        .doc-title {
            text-align: right;
            margin-top: -52px;
        }
        .doc-title h1 {
            font-size: 20px;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin: 0;
            color: #111;
        }
        .doc-title p {
            margin: 4px 0 0;
            font-size: 11px;
            color: #666;
        }

        .meta {
            width: 100%;
            margin-bottom: 28px;
        }
        .meta td {
            vertical-align: top;
            width: 50%;
            padding: 0;
        }
        .meta .label {
            font-size: 9px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 4px;
        }
        .meta .value { font-size: 12px; color: #222; line-height: 1.5; }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table.items thead th {
            background: #111;
            color: #f0c36d;
            text-align: left;
            font-size: 9px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 9px 10px;
        }
        table.items thead th.num { text-align: right; }
        table.items tbody td {
            padding: 9px 10px;
            border-bottom: 1px solid #e5e5e5;
            font-size: 12px;
        }
        table.items tbody td.num { text-align: right; }

        .totals { width: 100%; margin-top: 5px; }
        .totals td { padding: 5px 10px; font-size: 12px; }
        .totals .lbl { text-align: right; color: #666; }
        .totals .amt { text-align: right; width: 130px; }
        .totals .grand td {
            border-top: 2px solid #111;
            font-size: 15px;
            font-weight: bold;
            padding-top: 10px;
        }
        .totals .grand .amt { color: #b8860b; }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: #f0c36d;
            color: #111;
            font-weight: bold;
        }

        .footer {
            margin-top: 45px;
            padding-top: 15px;
            border-top: 1px solid #e5e5e5;
            font-size: 10px;
            color: #999;
            text-align: center;
            line-height: 1.6;
        }
    </style>
</head>
<body>
@php
    use Illuminate\Support\Str;

    $metodo = match($order->payment_method) {
        'card'  => 'Tarjeta',
        'cash'  => 'Contrareembolso',
        default => $order->payment_method ?? '—',
    };

    $estado = match($order->status) {
        'pending'   => 'Pendiente',
        'completed' => 'Completado',
        'cancelled' => 'Cancelado',
        default     => Str::ucfirst($order->status),
    };

    // El precio guardado ya es el total final. Mostramos un desglose de IVA
    // informativo (base + 21%) calculado hacia atrás.
    $totalFinal = (float) $order->total;
    $base       = round($totalFinal / 1.21, 2);
    $iva        = round($totalFinal - $base, 2);
@endphp

<div class="wrapper">

    <div class="header">
        <div class="brand">Aly<span>X</span></div>
        <div class="brand-sub">Motos · Taller · Merchandising</div>
        <div class="doc-title">
            <h1>Factura</h1>
            <p>Nº {{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

    <table class="meta">
        <tr>
            <td>
                <div class="label">Facturado a</div>
                <div class="value">
                    <strong>{{ $order->user->name ?? 'Cliente' }}</strong><br>
                    {{ $order->user->email ?? '' }}<br>
                    @if($order->shipping_address)
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_postal_code }} {{ $order->shipping_city }}<br>
                    @endif
                    @if($order->shipping_phone)
                        Tel. {{ $order->shipping_phone }}
                    @endif
                </div>
            </td>
            <td>
                <div class="label">Detalles del pedido</div>
                <div class="value">
                    <strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}<br>
                    <strong>Método de pago:</strong> {{ $metodo }}<br>
                    <strong>Estado:</strong> <span class="badge">{{ $estado }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="num">Precio unit.</th>
                <th class="num">Cantidad</th>
                <th class="num">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                @php
                    $precio   = (float) ($item['precio'] ?? 0);
                    $cantidad = (int) ($item['cantidad'] ?? 1);
                    $subtotal = $precio * $cantidad;
                @endphp
                <tr>
                    <td>{{ $item['nombre'] ?? 'Artículo' }}</td>
                    <td class="num">€{{ number_format($precio, 2) }}</td>
                    <td class="num">{{ $cantidad }}</td>
                    <td class="num">€{{ number_format($subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="lbl">Base imponible</td>
            <td class="amt">€{{ number_format($base, 2) }}</td>
        </tr>
        <tr>
            <td class="lbl">IVA (21%)</td>
            <td class="amt">€{{ number_format($iva, 2) }}</td>
        </tr>
        <tr class="grand">
            <td class="lbl">Total</td>
            <td class="amt">€{{ number_format($totalFinal, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        Gracias por tu compra en AlyX.<br>
        Esta factura se ha generado automáticamente · {{ now()->format('d/m/Y H:i') }}
    </div>

</div>
</body>
</html>
