<?php

namespace App\Http\Livewire\Admin;

use App\Models\Categoriagasto;
use Livewire\Component;
use App\Models\Rendicione;
use App\Models\Evento;
use App\Models\Rendiciongasto;
use Illuminate\Support\Facades\DB;

class RendicionEdit extends Component
{
    public $rend;
    public $totgastos;
    public $recaudado;
    public $aRendir;

    public $newDetalle;
    public $newValor;
    public $newCategoria;

    protected $rules = [
        //'newCategoria' => 'required',
        'newValor' => 'required'
    ];

    public function render()
    {
        $categorias= Categoriagasto::all();
        $lugar = Evento::find($this->rend->evento_id)->lugar;
        $gastos = $this->rend->gastos()->get();
        $this->totgastos = $this->rend->gastos()->sum('valor');
        $this->aRendir = $this->recaudado + $this->rend->a_cuenta - $this->totgastos;
        return view('livewire.admin.rendicion-edit', compact('gastos', 'lugar', 'categorias'));
    }

    public function mount(Rendicione $rendicion){
        $this->rend = $rendicion;
        $this->newCategoria=1;

        $this->recaudado = DB::SELECT('SELECT SUM(sub.importe) as recaudado FROM
            (SELECT reservas.id as res_id, eventos.id, MIN(funciones.fecha) as fecha, reservas.importe FROM eventos
 		    LEFT JOIN funciones on  eventos.id = funciones.evento_id
 		    LEFT JOIN funcione_reserva on funcione_reserva.funcione_id = funciones.id
            LEFT JOIN reservas on reservas.id = funcione_reserva.reserva_id
            where reservas.asist=1 and reservas.cancel =0
            GROUP BY res_id, eventos.id, reservas.importe) sub
            WHERE sub.id = ' . $this->rend->evento_id  . ' AND sub.fecha = "' . $this->rend->fecha .'"')
            [0]->recaudado;
    }

    public function changeACuenta(float $aCuenta){
        $this->rend->a_cuenta = $aCuenta;
        $this->rend->save();
    }

    public function changeCategoria(Rendiciongasto $gasto, int $categoria){
        $gasto->categoriagasto_id = $categoria;
        $gasto->save();
    }

    public function changeDetalle(Rendiciongasto $gasto, string $detalle){
        $gasto->detalle = $detalle;
        $gasto->save();
    }

    public function changeValor(Rendiciongasto $gasto, float $valor){
        $gasto->valor = $valor;
        $gasto->save();
    }
    public function deleteGasto(Rendiciongasto $gasto){
        $gasto->delete();
    }

    public function save(){
        $this->validate();

        $newgasto = new Rendiciongasto();
        $newgasto->rendicione_id= $this->rend->id;
        $newgasto->categoriagasto_id = $this->newCategoria;
        $newgasto->detalle = $this->newDetalle;
        $newgasto->valor = $this->newValor;
        
        $newgasto->save();

        $this->reset(['newDetalle', 'newValor']);

        
    }
}
