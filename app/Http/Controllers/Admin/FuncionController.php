<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use Illuminate\Http\Request;
use App\Models\Tema;
use App\Models\Funcione;

class FuncionController extends Controller
{


    public function index(Evento $evento)
    {
        return view('admin.funciones.index', compact('evento'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'tema_id' => 'required',
            'fecha' => 'required',
            'horario' => 'required',
            'capacidad' =>'required'
        ]);

        Funcione::create([
            'tema_id' => $request->tema_id,
            'fecha' => $request->fecha,
            'horario' => $request->horario,
            'capacidad' =>$request->capacidad,
            'evento_id' =>$request->evento_id
        ]);

        return redirect()->route('admin.eventos.show', $request->evento_id)->with('info', 'La Funcion se agregó con éxito');
    }


    public function show(Funcione $funcione)
    {
        return view('admin.funciones.show', compact('funcione'));
    }

 
    public function edit()
    {
        
    }

   
    public function update(Request $request, $id)
    {
      
    }

   
    public function destroy(Funcione $funcione)
    {
        $evento = $funcione->evento;

        $funcione->delete();
        return redirect()->route('admin.eventos.show', $evento)->with('info', 'La Funcion se eliminó con éxito');
    }

}
