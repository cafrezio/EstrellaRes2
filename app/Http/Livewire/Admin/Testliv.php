<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;



class Testliv extends Component
{

    protected $listeners = ['delete'];

    public $search;

    public function render()
    {
        $eventos = DB::table('eventos')
                ->select('eventos.id', 'eventos.lugar', 'eventos.activo', DB::raw('MIN(funciones.fecha)as inicio'), DB::raw('MAX(funciones.fecha) as final'))
                ->join('funciones', 'funciones.evento_id', '=', 'eventos.id')
                ->groupBy('eventos.id', 'eventos.lugar', 'eventos.activo')
                ->orderBy('activo', 'desc')
                ->orderBy('inicio', 'desc')
                ->get();
                
        return view('livewire.admin.testliv', compact('eventos'));
    }

    public function delete(Evento $evento){
        $evento->delete();
    }
}
