<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CargaTablaAusentes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cargatablaausentes:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inserta en la tabla de reservas ausentes los registros de las reservas que no se presentaron';

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
        DB::statement('INSERT INTO `reservas_ausentes`(`id`, `evt_id`, `lugar`, `usuario`, `telefono`, `codigo_res`, `importe`, `cant_adul`, `cant_esp`, `asist`, `cancel`, `f1_id`, `f1_tema`, `f1_fecha`, `f1_hora`, `f2_id`, `f2_tema`, `f2_fecha`, `f2_hora`) 
        SELECT res_uniq.id as reserva_id, evt_id, lugar, usuario, telefono, codigo_res, importe, cant_adul, cant_esp, asist, cancel,
                fun1.id as func1_id, fun1.titulo f1_titulo, fun1.fecha as f1_fecha, fun1.horario as f1_horario, fun2.id as func2_id, fun2.titulo as f2_titulo, fun2.fecha as f2_fecha, fun2.horario as f2_horario FROM 
                (SELECT DISTINCT res.id, evt.lugar, evt.id as evt_id, res.usuario, res.telefono, res.codigo_res, res.importe, res.cant_adul, res.cant_esp, res.asist, res.cancel FROM 
                reservas as res
                INNER JOIN funcione_reserva as funres on res.id = funres.reserva_id
                INNER JOIN funciones as fun ON fun.id = funres.funcione_id
                INNER JOIN temas as tem ON fun.tema_id = tem.id
                INNER JOIN eventos as evt ON fun.evento_id = evt.id
                WHERE evt.activo = 0 && asist=0 && evt.id > (SELECT MAX(evt_id) from reservas_ausentes)
                ORDER BY res.id  DESC) as res_uniq
                
                INNER JOIN (SELECT reserva_id, min(funres.funcione_id) as f1, MAX(funres.funcione_id) as f2 FROM
                funcione_reserva as funres
                GROUP By reserva_id) as funcs
                ON res_uniq.id = funcs.reserva_id
                
                INNER JOIN 
                (SELECT funciones.id, temas.titulo, funciones.fecha, funciones.horario FROM funciones INNER JOIN temas ON funciones.tema_id = temas.id) as 
                fun1 ON fun1.id = funcs.f1
                INNER JOIN (SELECT funciones.id, temas.titulo, funciones.fecha, funciones.horario FROM funciones INNER JOIN temas ON funciones.tema_id = temas.id) as 
                fun2 ON fun2.id = funcs.f2;');
    }
}
