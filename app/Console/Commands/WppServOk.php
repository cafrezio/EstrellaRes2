<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;

class WppServOk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wppservok:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');


        $cel = '+5491160208707';

        $stringDate = date('d-m-y H:i');
        $hora = 0;
        $hora = date('H', strtotime($stringDate));
        $evtAct = Evento::all()->where('activo','=','1')->count();

        if (!$evtAct || $hora <= 8) {
            return;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.wassenger.com/v1/devices/629b304cce503508260c325e/queue",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "token: 066f35090cd6e1403c8c62cb8fdfbb2cec1afa37f8522d85200245997ad75130f889c44eeb732f4a"
            ),
        ));

        $responseQ = json_decode(curl_exec($curl));
        curl_close($curl);


        $mens = $stringDate . ' - Wassenger-Ok ✔ - En Cola: ' . $responseQ->queue->size;

        $ocupacion = DB::select('SELECT ocup.id, ocup.lugar, date_format(ocup.fecha, "%d-%m") as fecha, ocup.CantRes, tot.Total, CONCAT(FORMAT(ocup.CantRes/tot.Total * 100, 0), "%")as Porc
        FROM (Select eventos.id, eventos.lugar, funciones.fecha, SUM(reservas.cant_adul + reservas.cant_esp) as CantRes from reservas 
        JOIN funcione_reserva ON reservas.id = funcione_reserva.reserva_id
        JOIN funciones ON funcione_reserva.funcione_id = funciones.id
        JOIN eventos on funciones.evento_id = eventos.id
        Where eventos.activo=1
        GROUP BY eventos.id, eventos.lugar, funciones.fecha
        ORDER BY funciones.fecha, eventos.id) ocup
        JOIN
        (Select eventos.id, funciones.fecha, SUM(funciones.capacidad) as Total from eventos
        JOIN funciones on eventos.id = funciones.evento_id
        Where eventos.activo=1
        GROUP BY eventos.id, funciones.fecha
        ORDER BY funciones.fecha, eventos.id) tot
        ON ocup.id = tot.id;');

        foreach ($ocupacion as $ocupEvt){
            $mens.= "\\n *$ocupEvt->lugar - $ocupEvt->Porc* \\n $ocupEvt->fecha - *$ocupEvt->CantRes* de $ocupEvt->Total \\n➖➖➖➖➖➖➖";
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
            CURLOPT_POSTFIELDS => "{\"phone\":\"" . $cel . "\",\"message\":\"" . $mens . "\"}",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Token: 066f35090cd6e1403c8c62cb8fdfbb2cec1afa37f8522d85200245997ad75130f889c44eeb732f4a"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        echo($mens);
        echo ($response);
    }
}
