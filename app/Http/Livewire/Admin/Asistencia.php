<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Evento;
use App\Models\Funcione;
use App\Models\Reserva;
use Illuminate\Support\Facades\DB;
use DateTime;

class Asistencia extends Component
{

    public $eventoSel;
    public $funcionSel;
    public $temaFunSel;

    public $importeGral;
    public $importeMen;

    public $totRes;
    public $totEvento;
    public $totAsist;
    public $totIng;

    public $newUsuario;
    public $newTelefono;
    public $newCantAdul;
    public $newCantEsp;
    public $newFun2;
    public $newImporte;

    public $fechaf1;

    protected $rules = [
        'newUsuario' => 'required|min:3'
    ];




    public function render()
    {
        $eventos = Evento::where('activo', "=", 1)
        ->get();

        $funciones = Evento::find($this->eventoSel)->temas_func()->sortBy('horario')->sortBy('fecha');

        $reservt = DB::select('SELECT res_uniq.id,  usuario, telefono, codigo_res, importe, cant_adul, cant_esp, 
        f1, f2, asist FROM 
        (SELECT DISTINCT res.id, res.usuario, res.telefono, res.codigo_res, res.importe, res.cant_adul, res.cant_esp, res.asist FROM 
        reservas as res
        INNER JOIN funcione_reserva as funres on res.id = funres.reserva_id
        ORDER BY res.id) as res_uniq
        INNER JOIN (SELECT reserva_id, min(funres.funcione_id) as f1, MAX(funres.funcione_id) as f2 FROM
        funcione_reserva as funres
        GROUP By reserva_id) as funcs
        ON res_uniq.id = funcs.reserva_id
        where f1 =' . $this->funcionSel . ' or f2=' . $this->funcionSel);

        $this->fechaf1 = new DateTime(Funcione::find($this->funcionSel)->fecha . " " . Funcione::find($this->funcionSel)->horario);
        $this->temaFunSel = Funcione::find($this->funcionSel)->tema_id;
        $this->totIng=0;
        $this->totRes=0;
        $this->totAsist=0;
        foreach ($reservt as $reserva) {
            $this->totRes += $reserva->cant_adul + $reserva->cant_esp;

            if(!$reserva->asist){
                continue;
            }

            $this->totAsist += $reserva->cant_adul + $reserva->cant_esp;
            $cantFunc = Reserva::find($reserva->id)->funciones()->count();

            if($cantFunc > 1){
                ($reserva->f1 != $this->funcionSel)? $resSec = $reserva->f1 : $resSec = $reserva->f2;
                
                $fechaf2 = new DateTime(Funcione::find($resSec)->fecha . " " . Funcione::find($resSec)->horario);
                if($fechaf2 < $this->fechaf1){
                   $reserva->importe=0;  
                } 
            }
            $this->totIng+= $reserva->importe;
        }

        $this->totEvento = DB::select('SELECT SUM(importe) as total from (SELECT DISTINCT(reservas.id), importe FROM reservas
        JOIN funcione_reserva on reservas.id = funcione_reserva.reserva_id
        JOIN funciones on funcione_reserva.funcione_id = funciones.id
        WHERE funciones.evento_id = 10 AND reservas.asist = 1) sub;')[0]->total;

        return view('livewire.admin.asistencia', compact('reservt', 'eventos', 'funciones'));
    }

    public function mount(){
        $this->eventoSel= Evento::where('activo', "=", 1)
            ->first()->id;

        $this->updatedEventoSel();

        $this->newCantAdul=1;
        $this->newCantEsp=0;
        $this->newImporte=$this->importeGral;
    }

    public function updatedEventoSel(){
        $this->funcionSel = Funcione::where('evento_id', "=", $this->eventoSel)
            ->first()->id;
        $this->importeGral = Evento::find($this->eventoSel)->precio;
        $this->importeMen = Evento::find($this->eventoSel)->precio_seg;
    }

    public function changeCantAdul(Reserva $res, $cant_adul){
        $cantFunc = $res->funciones()->count();
        $importe = ($res->cant_esp * $this->importeMen + $cant_adul * $this->importeGral) * $cantFunc;
        $res->cant_adul = $cant_adul;
        $res->importe = $importe;
        $res->save();
    }

    public function changeCantEsp(Reserva $res, $cant_esp){
        $cantFunc = $res->funciones()->count();
        $importe = ($cant_esp * $this->importeMen + $res->cant_adul * $this->importeGral) * $cantFunc;
        $res->cant_esp = $cant_esp;
        $res->importe = $importe;
        $res->save();
    }

    public function changeFunc2(Reserva $res, $func){
        $func_att = [$this->funcionSel];

        if ($func>0) {
            array_push($func_att, $func);
        }

        $res->funciones()->sync($func_att);
        $cantFunc = $res->funciones()->count();
        $importe = ($res->cant_esp * $this->importeMen + $res->cant_adul * $this->importeGral) * $cantFunc;
        $res->importe = $importe;
        $res->save();
    }

    public function changeAsist(Reserva $res, $asist){
        $res->update(['asist' => $asist]);
    }



    public function changeNewFun2($newFun2){
        $this->newFun2= $newFun2;
        $this->actImporte();
    }

    public function changeNewCantAdul($cant_adul){
        $this->newCantAdul= $cant_adul;
        $this->actImporte();
    }

    public function changeNewCantEsp($cant_esp){
        $this->newCantEsp= $cant_esp;
        $this->actImporte();
    }

    public function actImporte(){
        ($this->newFun2 > 0)? $cantfunc = 2 : $cantfunc = 1;
        $this->newImporte = ($this->newCantEsp * $this->importeMen + $this->newCantAdul * $this->importeGral) * $cantfunc;
    }

    public function save(){
        $this->validate();

        if($this->newTelefono == null)
        {
            $this->newTelefono = '1111';
        }
        
        $newRes = new Reserva();
        $newRes->usuario = $this->newUsuario;
        $newRes->telefono = $this->newTelefono;
        $newRes->importe = $this->newImporte;
        $newRes->cant_adul = $this->newCantAdul;
        $newRes->cant_esp = $this->newCantEsp;
        $newRes->asist = 1;
        $newRes->wppconf = '0';
        $newRes->wpprecord = '0';
        $newRes->codigo_res="123";

        $newRes->save();
        $newRes->codigo_res=str_pad($newRes->id, 4 ,"0", STR_PAD_LEFT);
        $newRes->save();

        $newRes->funciones()->attach($this->funcionSel);

        if ($this->newFun2<0) {
            $this->newFun2 = null;
        }

        if (!is_null($this->newFun2)) {
            $newRes->funciones()->attach($this->newFun2);
        }

        $this->reset(['newUsuario', 'newTelefono']);

        $this->newCantAdul=1;
        $this->newCantEsp=0;
        $this->newImporte=$this->importeGral;
        $this->newFun2=-1;

    }

    







}
