<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Evento;
use App\Models\Funcione;
use Illuminate\Support\Facades\DB;

class ReservaChart extends Component
{

    public $eventoSel;
    public $fechaSel;
    public $resTotal;

    public function render()
    {   
        $eventos = Evento::all()
                ->where('activo','=','1');

        $fechas = DB::table('funciones')
        ->select('fecha')
        ->distinct()
        ->where('funciones.evento_id', "=", $this->eventoSel)  
        ->orderBy('fecha')
        ->get();

        $this->datosTot();
        return view('livewire.admin.reserva-chart', compact('eventos','fechas'));
    }

    public function updatedfechaSel(){
        if($this->fechaSel > 0)
        {            
            $this->datos();
        }
    }

    public function updatedEventoSel(){
        if($this->eventoSel > 0)
        {
            setlocale(LC_TIME, "spanish");
            $fechas = DB::table('funciones')
            ->select('fecha')
            ->distinct()
            ->where('funciones.evento_id', "=", $this->eventoSel)  
            ->orderBy('fecha')
            ->get();
        
            $this->fechaSel = $fechas->first()->fecha;
        
            $this->datos();
        }

    }

    public function datos(){
        setlocale(LC_TIME, "spanish");
        $dat = DB::select("SELECT
                    funciones.id,
                    temas.titulo, 
                    funciones.fecha, 
                    funciones.horario, 
                    SUM(reservas.cant_adul + reservas.cant_esp) as reservas,
                    MAX(funciones.capacidad) - SUM(reservas.cant_adul + reservas.cant_esp) as disponible
                FROM `reservas`
                JOIN funcione_reserva ON reservas.id = funcione_reserva.reserva_id
                JOIN funciones ON funcione_reserva.funcione_id = funciones.id
                JOIN temas ON funciones.tema_id= temas.id
                WHERE funciones.evento_id = $this->eventoSel and funciones.fecha ='$this->fechaSel'
                GROUP BY 
                    funciones.id,
                    temas.titulo, 
                    funciones.fecha, 
                    funciones.horario
                ORDER BY 	
                    funciones.fecha, 
                    funciones.horario,
                    funciones.id");

        $categorias = array();
        $dataRes = array();
        $dataLib = array();
        $this->resTotal=0;
        foreach($dat as $funcion){
            array_push($categorias, $funcion->titulo . " - " . utf8_encode(strftime("%A %d de %B", strtotime($funcion->fecha)))  . " - " . strftime("%H:%M", strtotime($funcion->horario)));
            array_push($dataRes, $funcion->reservas);
            array_push($dataLib, $funcion->disponible);
            $this->resTotal += $funcion->reservas;
        }

        
        $titulo = Evento::find($this->eventoSel)->lugar . " - " . utf8_encode(strftime("%A %d de %B", strtotime($this->fechaSel)));
        $this->emit('graph', $titulo, $categorias, $dataRes, $dataLib);
    }


    public function datosTot(){
        setlocale(LC_TIME, "spanish");
        $dat = DB::select("SELECT res.evento_id, tot.lugar, tot.fecha, tot.entradas, res.cap FROM
        (SELECT funciones.evento_id, SUM(funciones.capacidad) as cap FROM funciones
        GROUP By funciones.evento_id ) res
        
        JOIN 
        (SELECT sub.id as evento_id, sub.lugar, sub.fecha,SUM(sub.cant_adul + sub.cant_esp) as entradas FROM
             (SELECT reservas.id as res_id, eventos.id, eventos.lugar, funciones.fecha, reservas.cant_adul, reservas.cant_esp FROM eventos
                     LEFT JOIN funciones on  eventos.id = funciones.evento_id
                     LEFT JOIN funcione_reserva on funcione_reserva.funcione_id = funciones.id
                    LEFT JOIN reservas on reservas.id = funcione_reserva.reserva_id 
                    WHERE eventos.activo=1) sub
        GROUP BY sub.id, sub.fecha ) tot
        ON res.evento_id = tot.evento_id  
        ORDER BY `tot`.`fecha`;");

        $categorias = array();
        $dataRes = array();
        $dataLib = array();
        foreach($dat as $evento){
            array_push($categorias, $evento->lugar . " - " . utf8_encode(strftime("%d/%m", strtotime($evento->fecha))));
            array_push($dataRes, $evento->entradas);
            array_push($dataLib, $evento->cap - $evento->entradas);
        }

        
        $titulo = "Totales";
        $this->emit('graph2', $titulo, $categorias, $dataRes, $dataLib);
    }

}
