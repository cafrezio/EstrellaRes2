<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Evento;
use App\Models\Reserva;
use App\Services\SaveResSheet;



class SaveRes extends Component
{
    protected $listeners = ['saveResU'];

    public function render()
    {
        return view('livewire.save-res');
    }

    public function saveResU(int $importe, string $usuario , string $telefono, int $cant_adul, int $cant_esp, $selectedFunc1, $selectedFunc2, int $evento_id){

        setlocale(LC_TIME, "spanish");

        $evt = Evento::find($evento_id);

        $reserva = new Reserva();

        $reserva->codigo_res='123';
        $reserva->importe=$importe;
        $reserva->usuario = $usuario;
        $reserva->telefono = $telefono;
        $reserva->cant_adul = $cant_adul;
        $reserva->cant_esp = $cant_esp;
        $reserva->wppconf = '0';
        $reserva->wpprecord = '0';

        $reserva->save();
        $reserva->codigo_res=str_pad($reserva->id, 4 ,"0", STR_PAD_LEFT);
        $reserva->save();

        $reserva->funciones()->attach($selectedFunc1);

        if (!is_null($selectedFunc2)) {
            $reserva->funciones()->attach($selectedFunc2);
        }

        $mensaje = 'Tu reserva ya está registrada.<br> Código de reserva: <b>' . str_pad($reserva->id, 4 ,"0", STR_PAD_LEFT) . 
        '</b><br>Te recomendamos que hagas una captura de pantalla para tener a mano esta info<br>
        <br><b>¿Cómo y cuándo se retiran las entradas?</b><br>Tenés que estar 30 min antes para asegurar tu lugar y abonar la entrada en el lugar del evento. Si no llegás las entradas pasan a disponibilidad
        <br><b>Medios de pago? | Solo en efectivo</b><br><br>Por favor sino vas al evento, avísanos por mensaje privado de Facebook o mensaje directo de Instagram, así la reserva se la damos a otra persona que si quiera ir! La reserva de entradas es un compromiso de asistencia  al evento. Pedimos por favor, que no nos fallen. Gracias!
        <br><br><b>PARA COMUNICARTE CON NOSOTROS:</b><br>FACEBOOK: MENSAJE PRIVADO EN: ESTRELLA DEL PLATA (PLANETARIO MOVIL).<br>INSTAGRAM: MENSAJE DIRECTO EN: @ESTRELLADELPLATA<br><b>Te esperamos!</b>'; 

        $resSheet = new SaveResSheet($reserva, $evt, $selectedFunc1, $selectedFunc2);
        
        //$resSheet->save();

        $resSheet->wppConf();

        $this->emit('alert', $mensaje, 'Listo!!', 'success');

        $this->emit('resetP');
    } 


}
