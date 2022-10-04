@php
    setlocale(LC_TIME, "spanish");
    $funcionid = 0;
    $total = 0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Rendicion - {{ $lugar }} - {{  utf8_encode(strftime("%d/%m/%Y", strtotime($rendicion->fecha))) }}</title>
    <style>
        .page-break {
            page-break-after: always;
        }
        body {
            font-family: "Source Sans Pro","Segoe UI","Roboto","Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
            margin-left: 10%;
            margin-top: 7%;
            font-size: 0.8em;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            border: 1px solid;
        }

        thead th{
            text-align: center;
        }

        th, td {
            text-align: left;
            padding: 5px;
        }

        tr:nth-child(even) {
            background-color: #c5c3c2;
            padding: 10px;
        }
        
        h1, h2, h3, h4{
            margin: 0;
            font-weight: ligther;
        }

        .container
        {

        }

    </style>
</head>
<body>
    <div class="container">
        <h1 style="font-size: 30px; font-weight:lighter">Rendición de Evento</h1>
        <hr><br>
        <table style="width:100%; border:1; ">
            <tbody>
                <tr>
                    <td>
                        <h4>Evento: <b>{{ $lugar }}</b></h4>
                    </td>
                    <td>
                        <h4>Fecha: <b>{{ utf8_encode(strftime("%a %d/%m/%Y", strtotime($rendicion->fecha))) }}</b></h4>
                    </td>
                    <td>
                        <h4>Cobrador: <b>{{ $cobrador }}</b></h4>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <h3>&nbsp;</h3>
        
        <h4>Gastos:</h4>
        <table border="1" cellpadding="1" cellspacing="1" style="width:100%">
            <thead>
                <tr>
                    <th scope="col" style="width:20%">Categor&iacute;a</th>
                    <th scope="col">Detalle</th>
                    <th scope="col" style="width:20%">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($gastos as $gasto)               
                    <tr>
                        <td>{{ $categorias[$gasto->categoriagasto_id] }}</td>
                        <td>{{ $gasto->detalle }}</td>
                        <td style="text-align: right">$ {{ number_format($gasto->valor,2,",",".") }}</td>
                    </tr>

                @endforeach
            </tbody>
        </table>
        
        <p>&nbsp;</p>
        
        <table align="right" border="0" cellpadding="1" cellspacing="1" style="width:300px; text-align:right">
                <tbody>
                    <tr>
                        <td style="text-align:right"><h3>Recaudado:</h3></td>
                        <td style="text-align:right; width:100px"><h3>$ {{ number_format($recaudado,2,',','.') }}</h3></td>
                    </tr>
                    <tr>
                        <td style="text-align:right"><h3>Total Gastos:</h3></td>
                        <td style="text-align:right"><h3>$ {{ number_format($totgastos,2,',','.') }}</h3></td>
                    </tr>
                    <tr>
                        <td style="text-align:right"><h3>A Cuenta:</h3></td>
                        <td style="text-align:right"><h3>$ {{ number_format($rendicion->a_cuenta,2,',','.') }}</h3></td>
                    </tr>
                    <tr>
                        <td style="text-align:right"><h3><b>A Rendir:</b></h3></td>
                        <td style="text-align:right"><h3><b>$ {{ number_format($aRendir,2,',','.') }}</b></h3></td>
                    </tr>
                </tbody>
            </table>

</div>
</body>
</html>