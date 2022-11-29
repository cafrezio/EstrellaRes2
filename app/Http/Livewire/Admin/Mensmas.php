<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Evento;
use App\Services\AvisoWpp;
use App\Models\Funcione;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class Mensmas extends Component
{
    public $eventoSel;
    public $funcionSel;
    public $mensaje;
    public $cantRes;


    protected $rules = [
        'eventoSel' => 'required',
        'funcionSel' => 'required|min:1',
        'mensaje' => 'required'
    ];

    public function render()
    {   
        $eventos = DB::table('eventos')
        ->select('eventos.id', 'eventos.lugar', 'eventos.activo', DB::raw('MIN(funciones.fecha)as inicio'), DB::raw('MAX(funciones.fecha) as final'))
        ->join('funciones', 'funciones.evento_id', '=', 'eventos.id')
        ->groupBy('eventos.id', 'eventos.lugar', 'eventos.activo')
        ->orderBy('activo', 'desc')
        ->orderBy('inicio', 'desc')
        ->limit(10)
        ->get();
        //$eventos = Evento::all();
        $funciones = Evento::find($this->eventoSel)->temas_func();     
        return view('livewire.admin.mensmas', compact('eventos', 'funciones'));
    }

    public function mount(){
        $this->eventoSel= Evento::first()->id;
    }

    public function save(){
        $this->validate();
        $avwpp = new AvisoWpp($this->funcionSel, $this->mensaje);
        $avwpp->wppAviso();

        $this->reset('mensaje');
        $this->emit('enviado');
    }

    public function updatedeventoSel(){
        $this->funcionSel= null;
    }

    public function updatedfuncionSel(){
        if(is_null($this->funcionSel)){
            $this->cantRes = -1;
        }
        else{
            $this->cantRes = Funcione::find($this->funcionSel)->reservas->count(); 
        }
    }


}
