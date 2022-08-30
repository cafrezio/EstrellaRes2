<?php

use App\Http\Controllers\Admin\Ocupacion;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventoController;
use App\Http\Livewire\ShowEventos;
use App\Http\Controllers\Admin\GeneraleController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\WebHookController;

Route::middleware(['auth:sanctum', 'verified'])->get('/admin', [IndexController::class, 'index'])->name('admin');

Route::middleware(['auth:sanctum', 'verified'])->middleware('can:admin.mensajes')->get('/admin/mensajes', function () {
    return view('admin.mensajes.index');
})->name('adminmens');

Route::middleware(['auth:sanctum', 'verified'])->middleware('can:admin.newreserva')->get('/admin/nuevareserva', function () {
    return view('admin.nuevareserva.index');
})->name('nuevareserva');

Route::middleware(['auth:sanctum', 'verified'])->middleware('can:admin.ocupacion')->get('/admin/ocupacion', [Ocupacion::class, 'index'])
->name('ocupacion');

Route::middleware(['auth:sanctum', 'verified'])->middleware('can:admin.asistenciafuncion')->get('/admin/asistencia', function () {
    return view('admin.asistencia.index');
})->name('asistencia');

Route::middleware(['auth:sanctum', 'verified'])->middleware('can:admin.asistenciagral')->get('/admin/asistenciagral', function () {
    return view('admin.asistenciagral.index');
})->name('asistencia');

Route::middleware(['auth:sanctum', 'verified'])->get('/admin/eventoprint/{id_event}', [EventoController::class, 'print'])
->name('eventoprint');

//Route::get('users/report', 'UsersController@report');

Route::post('/webhook', [WebHookController::class, 'handle']);

Route::get('/', ShowEventos::class)->name('home');

Route::get('/{id_event}', [EventoController::class, 'show'])->name('evento.show');




