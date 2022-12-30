<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WppEncAusentes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wppencausentes:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia consulta de asistencia a los ausentes';

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
        $ausentes = DB::table('reservas_ausentes')
        ->select('telefono', 'id')
        ->whereNull('estado')
        ->get();

        foreach($ausentes as $ausente)
        {
            $cel = "549". $ausente->telefono;
            $this->enviarMenu($cel);
            DB::table('reservas_ausentes')
            ->where('id', '=', $ausente->id)
            ->update(['estado' => 'menu']);
        }
    }

    function enviarMenu($cel)
    {
        $postFields = '{
            "phone": "' . $cel . '",
            "device": "629b304cce503508260c325e",
            "order": true,
            "list": {
                "description": "Siempre queremos mejorar nuestro servicio, por lo que te pedimos si no te molesta, nos cuentes por qué no viniste",
                "button": "Elegí una opción",
                "title": "😔 Nos apena que no hayas venido al Planetario Móvil..",
                "sections": [
                    {
                        "title": "No fuí por...",
                        "rows": [
                            {
                                "id": "asistio",
                                "title": "🖐 Si asistí"
                            },
                            {
                                "id": "economica",
                                "title": "💵 Costo de entradas",
                                "description": "Decidí no asistir por una cuestión económica"
                            },
                            {
                                "id": "clima",
                                "title": "🌧 Clima",
                                "description": "No fuí debido al estado del tiempo el día de la función"
                            },
                            {
                                "id": "arrepentido",
                                "title": "🤷‍♂️ Me arrepentí",
                                "description": "Me arrepentí de la reserva, ya no tenia ganas de ir."
                            },
                            {
                                "id": "otra",
                                "title": "Otra Razón",
                                "description": "No pude ir por otra razón (no hacen falta detalles)."
                            }
                            ]
                        }
                        ]
                    }
                }';
        $this->sendMessage($postFields);
    }

    function sendMessage($postFields)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.wassenger.com/v1/messages",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => array(
                "Token: 066f35090cd6e1403c8c62cb8fdfbb2cec1afa37f8522d85200245997ad75130f889c44eeb732f4a",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
        
    }
}
