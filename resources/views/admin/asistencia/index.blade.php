@extends('adminlte::page')

@section('title','Estrella del Plata')

@section('content')
    <h1 style="padding:7px"><i class="fas fa-clipboard-check"></i> </i>&nbsp;&nbsp;Asistencia</h1>
    <div class="card">
        @livewire('admin.asistencia')
    </div>
    <div id="table_asist" style="height: 70vh">
		
	</div>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin.css">
    <style>
        th{
            position: sticky;
            top: 0;
            background-color: black;
            color: white;
        }

        .rowh {
            height: auto !important;
            padding-top: 0px !important;
            padding-bottom: 0px !important;
        }
    </style>
@stop



