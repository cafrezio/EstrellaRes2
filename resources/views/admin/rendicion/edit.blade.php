@extends('adminlte::page')

@section('title','Estrella del Plata')

@section('content')
    <h1 style="padding:7px"><i class="fas fa-money-bill-alt"></i> </i>&nbsp;&nbsp;Editar Rendición</h1>
        @livewire('admin.rendicion-edit', ['rendicion'=>$rendicion]) 
@stop

