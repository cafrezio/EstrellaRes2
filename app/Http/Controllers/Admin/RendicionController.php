<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoriagasto;
use Illuminate\Http\Request;
use App\Models\Rendicione;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class RendicionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Rendicione $rendicion)
    {
        if(Auth::user()->hasRole('Cobrador') && ($rendicion->user_id != Auth::user()->id))
        {
            return view('admin.rendiciones.index');
        }
        return view('admin.rendicion.edit', compact('rendicion'));
    }

    public function print(int $rend)
    {
        $rendicion = Rendicione::find($rend);
        $recaudado = DB::SELECT('SELECT SUM(sub.importe) as recaudado FROM
            (SELECT reservas.id as res_id, eventos.id, MIN(funciones.fecha) as fecha, reservas.importe FROM eventos
 		    LEFT JOIN funciones on  eventos.id = funciones.evento_id
 		    LEFT JOIN funcione_reserva on funcione_reserva.funcione_id = funciones.id
            LEFT JOIN reservas on reservas.id = funcione_reserva.reserva_id
            where reservas.asist=1 and reservas.cancel =0
            GROUP BY res_id, eventos.id, reservas.importe) sub
            WHERE sub.id = ' . $rendicion->evento_id  . ' AND sub.fecha = "' . $rendicion->fecha .'"')
            [0]->recaudado;

        $categorias = Categoriagasto::pluck('categoria', 'id');
        $lugar = Evento::find($rendicion->evento_id)->lugar;
        $gastos = $rendicion->gastos()->get();
        $totgastos = $rendicion->gastos()->sum('valor');
        $aRendir = $recaudado + $rendicion->a_cuenta - $totgastos;      
        $cobrador = User::find($rendicion->user_id)->name;

        $pdf = PDF::loadView('admin.rendiciones.print', compact('rendicion', 'recaudado', 'lugar', 
            'gastos', 'totgastos', 'aRendir', 'cobrador', 'categorias'));
        return $pdf->stream('rendicion_'.$lugar.'_'.utf8_encode(strftime("%d-%m-%Y", strtotime($rendicion->fecha))).'.pdf');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
