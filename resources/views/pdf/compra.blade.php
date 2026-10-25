@extends('pdf.layout')

@section('title', 'Orden de compra '.$compra->numero_compra)
@section('doc_type', 'Orden de compra')
@section('doc_number', $compra->numero_compra)

@section('content')
    <table class="info">
        <tr>
            <td style="width: 55%;">
                <strong>Proveedor:</strong> {{ $compra->proveedor?->nombre }}<br>
                <strong>{{ $compra->proveedor?->tipo_documento }}:</strong> {{ $compra->proveedor?->numero_documento }}<br>
                <strong>Contacto:</strong> {{ $compra->proveedor?->contacto ?? '—' }}<br>
                <strong>Teléfono:</strong> {{ $compra->proveedor?->telefono ?? '—' }}
            </td>
            <td style="width: 45%;">
                <strong>Fecha:</strong> {{ $compra->fecha_compra->format('d/m/Y') }}<br>
                <strong>Estado:</strong> <span class="badge">{{ ucfirst($compra->estado) }}</span><br>
                <strong>Factura proveedor:</strong> {{ $compra->factura_proveedor ?? '—' }}<br>
                <strong>Registró:</strong> {{ $compra->user?->name }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Ficha</th>
                <th class="right">Pedida</th>
                <th class="right">Recibida</th>
                <th class="right">Costo unit.</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($compra->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->ficha?->denominacion }}</td>
                    <td class="right">{{ $detalle->cantidad }}</td>
                    <td class="right">{{ $detalle->cantidad_recibida }}</td>
                    <td class="right">${{ number_format((float) $detalle->costo_unitario, 0, ',', '.') }}</td>
                    <td class="right">${{ number_format((float) $detalle->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td class="right">${{ number_format((float) $compra->subtotal, 0, ',', '.') }}</td></tr>
        <tr><td>IVA (19%)</td><td class="right">${{ number_format((float) $compra->impuesto, 0, ',', '.') }}</td></tr>
        <tr class="grand"><td>TOTAL</td><td class="right">${{ number_format((float) $compra->total, 0, ',', '.') }}</td></tr>
    </table>

    @if ($compra->notas)
        <p><strong>Notas:</strong> {{ $compra->notas }}</p>
    @endif

    <table class="signatures">
        <tr>
            <td><span>Elaboró</span></td>
            <td><span>Proveedor</span></td>
        </tr>
    </table>
@endsection