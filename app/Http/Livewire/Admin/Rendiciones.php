<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Rendicione;
use Illuminate\Support\Facades\Auth;


class Rendiciones extends Component
{

    public $eventoSel;
    public $fechaSel;

    public $eventosRend;
    public $newRend;

    public $user_id;

    protected $listeners = ['deleteRendicion'];

    public function render()
    {
        if(Auth::user()->hasRole('Cobrador')){

            $this->eventosRend = DB::select('SELECT rendiciones.id as rend_id, ev_rec.evento_id, ev_rec.lugar, ev_rec.fecha, 
            users.name, ev_rec.recaudado, rendiciones.a_cuenta,  gast.gastos, (COALESCE(ev_rec.recaudado,0) + COALESCE(rendiciones.a_cuenta,0) - COALESCE(gast.gastos,0)) a_rendir FROM
            (SELECT sub.id as evento_id, sub.lugar, sub.fecha, SUM(sub.importe) as recaudado FROM
            (SELECT reservas.id as res_id, eventos.id, eventos.lugar, MIN(funciones.fecha) as fecha, reservas.importe FROM eventos
 		    LEFT JOIN funciones on  eventos.id = funciones.evento_id
 		    LEFT JOIN funcione_reserva on funcione_reserva.funcione_id = funciones.id
            LEFT JOIN reservas on reservas.id = funcione_reserva.reserva_id
            where reservas.asist=1 and reservas.cancel =0 and eventos.activo=1
            GROUP BY res_id, eventos.id, eventos.lugar, reservas.importe) sub
            GROUP BY sub.id, sub.lugar, sub.fecha) ev_rec
            JOIN rendiciones ON ev_rec.evento_id = rendiciones.evento_id AND ev_rec.fecha = rendiciones.fecha
            LEFT JOIN users ON rendiciones.user_id = users.id
            LEFT JOIN 
            (SELECT rendicione_id, SUM(valor) as gastos FROM rendiciongastos
            GROUP by rendicione_id) gast
            on rendiciones.id = gast.rendicione_id
            WHERE users.id = ' .  Auth::user()->id  .
            ' ORDER BY ev_rec.fecha DESC;
            ') ;


            $this->newRend = DB::select('SELECT rtod.evento_id, rtod.lugar, rtod.fecha FROM 
            (SELECT eventos.id evento_id, eventos.lugar, fecha FROM eventos 
            JOIN evento_user ON eventos.id = evento_user.evento_id
            JOIN users ON evento_user.user_id = users.id
            JOIN (SELECT DISTINCT(fecha) fecha,  evento_id FROM funciones) evf
            ON eventos.id = evf.evento_id
            WHERE eventos.activo=1 AND users.id = ' .  Auth::user()->id  . ') rtod
            LEFT JOIN rendiciones
            ON rtod.evento_id = rendiciones.evento_id and rtod.fecha = rendiciones.fecha
            WHERE rendiciones.evento_id IS NULL;');
        }
        else
        {
            $this->eventosRend = DB::select('SELECT rendiciones.id as rend_id, ev_rec.evento_id, ev_rec.lugar, ev_rec.fecha, 
            users.name, ev_rec.recaudado, rendiciones.a_cuenta,  gast.gastos, (COALESCE(ev_rec.recaudado,0) + COALESCE(rendiciones.a_cuenta,0) - COALESCE(gast.gastos,0)) a_rendir FROM
            (SELECT sub.id as evento_id, sub.lugar, sub.fecha, SUM(sub.importe) as recaudado FROM
            (SELECT reservas.id as res_id, eventos.id, eventos.lugar, MIN(funciones.fecha) as fecha, reservas.importe FROM eventos
 		    LEFT JOIN funciones on  eventos.id = funciones.evento_id
 		    LEFT JOIN funcione_reserva on funcione_reserva.funcione_id = funciones.id
            LEFT JOIN reservas on reservas.id = funcione_reserva.reserva_id
            where reservas.asist=1 and reservas.cancel =0
            GROUP BY res_id, eventos.id, eventos.lugar, reservas.importe) sub
            GROUP BY sub.id, sub.lugar, sub.fecha) ev_rec
            JOIN rendiciones ON ev_rec.evento_id = rendiciones.evento_id AND ev_rec.fecha = rendiciones.fecha
            LEFT JOIN users ON rendiciones.user_id = users.id
            LEFT JOIN 
            (SELECT rendicione_id, SUM(valor) as gastos FROM rendiciongastos
            GROUP by rendicione_id) gast
            on rendiciones.id = gast.rendicione_id
            ORDER BY ev_rec.fecha DESC;
            ') ;
        }

        return view('livewire.admin.rendiciones');
    }

    public function mount(){
        $this->user_id = Auth::user()->id;

        /*
        if(Auth::user()->hasRole('Cobrador')){

            $this->eventosRend = DB::select('SELECT rendiciones.id as rend_id, ev_rec.evento_id, ev_rec.lugar, ev_rec.fecha, 
            users.name, ev_rec.recaudado, rendiciones.a_cuenta,  gast.gastos, (COALESCE(ev_rec.recaudado,0) + COALESCE(rendiciones.a_cuenta,0) - COALESCE(gast.gastos,0)) a_rendir FROM
            (SELECT sub.id as evento_id, sub.lugar, sub.fecha, SUM(sub.importe) as recaudado FROM
            (SELECT reservas.id as res_id, eventos.id, eventos.lugar, MIN(funciones.fecha) as fecha, reservas.importe FROM eventos
 		    LEFT JOIN funciones on  eventos.id = funciones.evento_id
 		    LEFT JOIN funcione_reserva on funcione_reserva.funcione_id = funciones.id
            LEFT JOIN reservas on reservas.id = funcione_reserva.reserva_id
            where reservas.asist=1 and reservas.cancel =0
            GROUP BY res_id, eventos.id, eventos.lugar, reservas.importe) sub
            GROUP BY sub.id, sub.lugar, sub.fecha) ev_rec
            JOIN rendiciones ON ev_rec.evento_id = rendiciones.evento_id AND ev_rec.fecha = rendiciones.fecha
            LEFT JOIN users ON rendiciones.user_id = users.id
            LEFT JOIN 
            (SELECT rendicione_id, SUM(valor) as gastos FROM rendiciongastos
            GROUP by rendicione_id) gast
            on rendiciones.id = gast.rendicione_id
            WHERE users.id = ' .  Auth::user()->id  .
            ' ORDER BY ev_rec.fecha DESC;
            ') ;


            $this->newRend = DB::select('SELECT rtod.evento_id, rtod.lugar, rtod.fecha FROM 
            (SELECT eventos.id evento_id, eventos.lugar, fecha FROM eventos 
            JOIN evento_user ON eventos.id = evento_user.evento_id
            JOIN users ON evento_user.user_id = users.id
            JOIN (SELECT DISTINCT(fecha) fecha,  evento_id FROM funciones) evf
            ON eventos.id = evf.evento_id
            WHERE users.id = ' .  Auth::user()->id  . ') rtod
            LEFT JOIN rendiciones
            ON rtod.evento_id = rendiciones.evento_id and rtod.fecha = rendiciones.fecha
            WHERE rendiciones.evento_id IS NULL;');
        }
        else
        {
            $this->eventosRend = DB::select('SELECT rendiciones.id as rend_id, ev_rec.evento_id, ev_rec.lugar, ev_rec.fecha, 
            users.name, ev_rec.recaudado, rendiciones.a_cuenta,  gast.gastos, (COALESCE(ev_rec.recaudado,0) + COALESCE(rendiciones.a_cuenta,0) - COALESCE(gast.gastos,0)) a_rendir FROM
            (SELECT sub.id as evento_id, sub.lugar, sub.fecha, SUM(sub.importe) as recaudado FROM
            (SELECT reservas.id as res_id, eventos.id, eventos.lugar, MIN(funciones.fecha) as fecha, reservas.importe FROM eventos
 		    LEFT JOIN funciones on  eventos.id = funciones.evento_id
 		    LEFT JOIN funcione_reserva on funcione_reserva.funcione_id = funciones.id
            LEFT JOIN reservas on reservas.id = funcione_reserva.reserva_id
            where reservas.asist=1 and reservas.cancel =0
            GROUP BY res_id, eventos.id, eventos.lugar, reservas.importe) sub
            GROUP BY sub.id, sub.lugar, sub.fecha) ev_rec
            JOIN rendiciones ON ev_rec.evento_id = rendiciones.evento_id AND ev_rec.fecha = rendiciones.fecha
            LEFT JOIN users ON rendiciones.user_id = users.id
            LEFT JOIN 
            (SELECT rendicione_id, SUM(valor) as gastos FROM rendiciongastos
            GROUP by rendicione_id) gast
            on rendiciones.id = gast.rendicione_id
            ORDER BY ev_rec.fecha DESC;
            ') ;
        }*/
    }

    public function save($evento_id, $fecha){
        $rend = new Rendicione;
        $rend->evento_id = $evento_id;
        $rend->fecha = strftime("%Y/%m/%d", $fecha);
        $rend->user_id = $this->user_id;

        $rend->save();

        return redirect()->route('admin.rendicion.edit', $rend);
    }

    public function deleteRendicion(Rendicione $rend){
        $rend->delete();
    }
}
