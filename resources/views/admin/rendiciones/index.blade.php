@extends('adminlte::page')

@section('title','Estrella del Plata')

@section('content')
    <h1 style="padding:7px"><i class="fas fa-money-bill-alt"></i> </i>&nbsp;&nbsp;Rendiciones</h1>
            @livewire('admin.rendiciones')
@stop

@section('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                
    <script>
        Livewire.on('deleteRend', rendId =>{
            Swal.fire({
                title: 'Está seguro que desea eliminar la Rendición?',
                text: "No se puede revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Si. Eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit('deleteRendicion', rendId);
                    Swal.fire(
                    'Eliminado!',
                    'La rendicion ha sido eliminada.',
                    'success'
                    )
                }
            })  
        });
        
    </script>
 
@stop

