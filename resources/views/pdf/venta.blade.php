@extends('pdf.layout')

@section('title', 'Factura '.$venta->numero_factura)
@section('doc_type', 'Factura de venta')
@section('doc_number', $venta->numero_factura)

@section('content')
    <table class="info">
        <tr>
            <td style="width: 55%;">
                <strong>Cliente:</strong> {{ $venta->cliente?->nombre }}<br>
                <strong>Documento:</strong> {{ $venta->cliente?->documento }}<br>
                <strong>Nivel VIP:</strong> {{ ucfirst($venta->cliente?->nivel_vip ?? '—') }}
            </td>
            <td style="width: 45%;">
                <strong>Fecha:</strong> {{ $venta->fecha_venta->format('d/m/Y') }}<br>
                <strong>Estado:</strong> <span class="badge">{{ ucfirst($venta->estado) }}</span><br>
                <strong>Cajero:</strong> {{ $venta->user?->name }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Ficha</th>
                <th class="right">Cantidad</th>
                <th class="right">Valor unit.</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->ficha?->denominacion }}</td>
                    <td class="right">{{ $detalle->cantidad }}</td>
                    <td class="right">${{ number_format((float) $detalle->valor_unitario, 0, ',', '.') }}</td>
                    <td class="right">${{ number_format((float) $detalle->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td class="right">${{ number_format((float) $venta->subtotal, 0, ',', '.') }}</td></tr>
        <tr><td>IVA (19%)</td><td class="right">${{ number_format((float) $venta->impuesto, 0, ',', '.') }}</td></tr>
        <tr class="grand"><td>TOTAL</td><td class="right">${{ number_format((float) $venta->total, 0, ',', '.') }}</td></tr>
    </table>

    @if ($venta->notas)
        <p><strong>Notas:</strong> {{ $venta->notas }}</p>
    @endif

    <table class="signatures">
        <tr>
            <td><span>Cajero</span></td>
            <td><span>Cliente</span></td>
        </tr>
    </table>
@endsection