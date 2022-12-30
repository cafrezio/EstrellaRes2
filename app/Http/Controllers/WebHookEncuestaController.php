<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebHookEncuestaController extends Controller
{
    
    public function handle(Request $request)
    {
        if (!isset($request->data['body'])) {
            echo "NO BODY";
            return;
        }

        $cel = $request->data['fromNumber'];
        $type = $request->data['type'];
        $celbd = str_replace('+549', '', $cel);

        if (!$this->findNumber($celbd)) {
            return;
        }

        $state = $this->stateNumber($celbd);
        echo $state;
        switch ($state) {
            case null:
            case 'menu':
                if ($type != 'list_response') {
                    $this->enviaMensajeError($cel);
                    $this->enviarMenu($cel);
                } else {
                    $idSel = $request->data['quoted']['selectedId'];
                    $this->guardarRespuesta($celbd, $idSel);
                    $this->eviaMensajeGracias($cel);
                }
                break;

            case 'finalizado': {
                    $this->enviaMensajeContacto($cel);
                }
                break;
        }
    }


    
    function findNumber($phone)
    {
        $ex = DB::table('reservas_ausentes')
            ->select('telefono')
            ->where('telefono', '=', $phone)
            ->count();

        return $ex > 0;
    }

    function stateNumber($phone)
    {
        $state = DB::table('reservas_ausentes')
            ->select('estado')
            ->where('telefono', '=', $phone)
            ->orderBy('id', 'desc')
            ->first()
            ->estado;

        return $state;
    }

    function enviaMensajeError($cel)
    {
        $message = "🤖 Lo siento, no entiendo tu respuesta. Por favor, selecciona una opción del menú.";
        $postFields = '{
            "phone": "' . $cel . '",
            "device": "' . env('WPP_DEVICE_ENC') . '",
            "order": true,
            "message": "' . $message . '"
        }';

        $this->sendMessage($postFields);
    }

    function enviarMenu($cel)
    {
        $postFields = '{
            "phone": "' . $cel . '",
            "device": "' . env('WPP_DEVICE_ENC') . '",
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

    function guardarRespuesta($celbd, $idSel)
    {
        DB::table('reservas_ausentes')
            ->where('telefono', '=', $celbd)
            ->orderBy('id', 'desc')
            ->limit(1)
            ->update(['causa' => $idSel, 'estado' => 'finalizado']);

    }

    function eviaMensajeGracias($cel)
    {     
        $postFields = '{
            "phone": "' . $cel . '",
            "device": "' . env('WPP_DEVICE_ENC') . '",
            "order": true,
            "header": "🤖 Muchas Gracias!",
            "message": "Tu respuesta nos ayudará a mejorar nuestro servicio. Te esperamos la próxima vez!👋. Puedes seguirnos en nuestras redes sociales para enterarte donde vamos a estar:",
            "buttons": [
                {
                    "id": "face",
                    "kind": "url",
                    "text": "Facebook",
                    "value": "https://www.facebook.com/EstrellaDelPlataPlanetarioMovil"
                },
                {
                    "id": "insta",
                    "kind": "url",
                    "text": "Instagram",
                    "value": "https://www.instagram.com/estrelladelplata/"
                }
                ]
            }';
        $this->sendMessage($postFields);
    }

    function enviaMensajeContacto($cel)
    {
        $postFields = '{
            "phone": "' . $cel . '",
            "device": "' . env('WPP_DEVICE_ENC') . '",
            "order": true,
            "header": "🤖 No entiendo...",
            "message": "Lo siento, no entiendo tu mensaje, ante cualquier consulta puedes comunicarte con nosotros en nuestras redes sociales:",
            "buttons": [
                {
                    "id": "face",
                    "kind": "url",
                    "text": "Facebook",
                    "value": "https://www.facebook.com/EstrellaDelPlataPlanetarioMovil"
                },
                {
                    "id": "insta",
                    "kind": "url",
                    "text": "Instagram",
                    "value": "https://www.instagram.com/estrelladelplata/"
                }
                ]
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
