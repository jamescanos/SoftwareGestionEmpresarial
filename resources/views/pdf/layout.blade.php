<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <style>
        @page { margin: 120px 40px 70px 40px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #222; }
        header { position: fixed; top: -95px; left: 0; right: 0; height: 80px; }
        footer { position: fixed; bottom: -50px; left: 0; right: 0; height: 30px;
                 font-size: 9px; color: #777; text-align: center; border-top: 1px solid #ccc; padding-top: 5px; }
        .company { font-size: 16px; font-weight: bold; color: #991b1b; }
        .muted { color: #666; }
        .doc-box { border: 2px solid #991b1b; padding: 6px 10px; text-align: center; }
        .doc-box .type { font-size: 10px; text-transform: uppercase; color: #991b1b; }
        .doc-box .number { font-size: 16px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        .info td { padding: 3px 4px; vertical-align: top; }
        .items { margin-top: 15px; }
        .items th { background: #991b1b; color: #fff; padding: 6px; font-size: 10px; text-align: left; }
        .items td { padding: 5px 6px; border-bottom: 1px solid #ddd; }
        .right { text-align: right !important; }
        .totals { width: 40%; margin-left: 60%; margin-top: 10px; }
        .totals td { padding: 4px 6px; }
        .totals .grand td { font-size: 13px; font-weight: bold; border-top: 2px solid #991b1b; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 8px; background: #eee; font-size: 10px; }
        .signatures { margin-top: 60px; }
        .signatures td { width: 50%; text-align: center; padding-top: 35px; }
        .signatures span { border-top: 1px solid #333; padding: 4px 30px 0 30px; }
    </style>
</head>
<body>
    <header>
        <table>
            <tr>
                <td style="width: 65%;">
                    {{-- Cambia estos datos de relleno por los reales de tu casino --}}
                    <div class="company">Casino Fortuna S.A.S.</div>
                    <div class="muted">NIT 900.000.000-0 · Santa Rosa de Cabal, Risaralda</div>
                    <div class="muted">Tel. 606 000 0000 · contacto@casinofortuna.com</div>
                </td>
                <td style="width: 35%;">
                    <div class="doc-box">
                        <div class="type">@yield('doc_type')</div>
                        <div class="number">@yield('doc_number')</div>
                    </div>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        Documento generado por el ERP el {{ now()->format('d/m/Y H:i') }} — Software de Gestión Empresarial · COTECNOVA
    </footer>

    <main>
        @yield('content')
    </main>
</body>
</html>