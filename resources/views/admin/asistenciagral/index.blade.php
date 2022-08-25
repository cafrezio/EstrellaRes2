@extends('adminlte::page')

@section('title','Estrella del Plata')

@section('content')
    <h1 style="padding:7px"><i class="fas fa-chart-line"></i> </i>&nbsp;&nbsp;Asistencia General</h1>
    <div class="card">
        @livewire('admin.asistencia-gral')
    </div>
    <div id="container-graph" style="height: 70vh">
		
	</div>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin.css">
@stop

@section('js')

    <script src="https://code.highcharts.com/highcharts.js"></script>

    <script>
    Livewire.on('graph', function(titulo, categorias, dataAsis, dataAus){
        //document.write(Array.from(dataRes));
        const asistencia = [];
        dataAsis.forEach(myFunction);
        function myFunction(item) {
            asistencia.push(parseInt(item));
        }

        const ausentes = [];
        dataAus.forEach(myFunction2);
        function myFunction2(item) {
            ausentes.push(parseInt(item));
        }
        //document.write(vals);

        Highcharts.chart('container-graph', {
		    chart: {
		        type: 'bar'
		    },
		    title: {
		        text: titulo
		    },
		    xAxis: {
		        categories: categorias
		    },
		    yAxis: {
		        title: {
		            text: 'Reservas'
		        }
		    },
		    legend: {
		        reversed: true
		    },
		    plotOptions: {
		        series: {
		            stacking: 'normal',
		            dataLabels: {
                	enabled: true
            		}
		        },
		        
		    },
		    series: [{
		        name: 'Ausentes',
		        data: ausentes,
                color: '#E74C3C'
		    }, {
		        name: 'Asistencia',
		        data: asistencia,
		        color: '#1ABC9C'
		    }]
		});
    })

    </script>
@stop
