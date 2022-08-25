<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;


class AsistenciaGral extends Component
{

    public $eventoSel;
    public $fechaSel;
    public $resTotal;
    public $asistTotal;

    public function render()
    {
        $eventos = Evento::all();
        $fechas = DB::table('funciones')
        ->select('fecha')
        ->distinct()
        ->where('funciones.evento_id', "=", $this->eventoSel)  
        ->orderBy('fecha')
        ->get();

        return view('livewire.admin.asistencia-gral', compact('eventos','fechas'));
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
                    SUM(-(reservas.cant_adul + reservas.cant_esp) * (reservas.asist - 1)) as ausentes,
                    SUM((reservas.cant_adul + reservas.cant_esp) * reservas.asist) as asistencia
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
        $dataAsis = array();
        $dataAus = array();
        $this->resTotal=0;
        $this->asistTotal=0;
        foreach($dat as $funcion){
            array_push($categorias, $funcion->titulo . " - " . utf8_encode(strftime("%A %d de %B", strtotime($funcion->fecha)))  . " - " . strftime("%H:%M", strtotime($funcion->horario)));
            array_push($dataAsis, $funcion->asistencia);
            array_push($dataAus, $funcion->ausentes);
            $this->resTotal += $funcion->asistencia + $funcion->ausentes;
            $this->asistTotal += $funcion->asistencia;
        }

        
        $titulo = Evento::find($this->eventoSel)->lugar . " - " . utf8_encode(strftime("%A %d de %B", strtotime($this->fechaSel)));
        $this->emit('graph', $titulo, $categorias, $dataAsis, $dataAus);
    }
}
