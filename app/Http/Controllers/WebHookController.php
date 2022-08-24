<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use Illuminate\Support\Facades\DB;

class WebHookController extends Controller
{
    public function handle(Request $request)
  { 
    $mensIn = $request->data['body'];
    $cel = $request->data['fromNumber'];
    
    $mens_proc = strtolower(trim($mensIn));
    $cel_proc = substr($cel, 4);

    if($mens_proc == "cancelar")
    {
      
      	echo $cel_proc . '\n';
      
        $reserv = DB::table('reservas')
        ->join('funcione_reserva', 'funcione_reserva.reserva_id', '=', 'reservas.id')
        ->join('funciones', 'funciones.id', '=', 'funcione_reserva.funcione_id')
        ->join('eventos', 'funciones.evento_id', '=', 'eventos.id')
        ->select('reservas.id')
        ->where('telefono', '=', $cel_proc)
        ->where('eventos.activo', '=', '1')
        ->orderBy('reservas.id', 'desc')
        ->first();
    

        if($reserv != null)
        {
          if(Reserva::find($reserv->id)->delete()){
            $mens = "Tu reserva fue cancelada. Podés realizar una nueva reserva desde https://estrellareservas.com/";
          }
        }
        else{
          $mens = "No hay reservas registradas con este numero de telefono";
        }
 
    }
    else
    {
      $mens = "Hola!  \\n\\n";
      $mens.= "comunicarte por:\\n\\n";
      $mens.= "*Facebook* \\n";
      $mens.= "👉  https://m.me/EstrellaDelPlataPlanetarioMovil\\n\\n";
      $mens.= "*Instagram* \\n";
      $mens.= "👉  https://www.instagram.com/estrelladelplata\\n";
      $mens.= "➖➖➖➖➖➖➖\\n";
      $mens.= "Gracias";
    }

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.wassenger.com/v1/messages",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\"phone\":\"". $cel. "\",\"message\":\"" . $mens . "\"}",
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Token: 066f35090cd6e1403c8c62cb8fdfbb2cec1afa37f8522d85200245997ad75130f889c44eeb732f4a"
    ],
    ]);

    $response = curl_exec($curl);

  }
}
