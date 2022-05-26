<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\TecnicoController;

Route::post('solicitud/peticion', [SolicitudController::class, 'peticion_store'])->name('solicitud.store.peticion');
Route::put('solicitud/peticion/{solicitud}', [SolicitudController::class, 'peticion_update'])->name('solicitud.update.peticion');
Route::get('solicitud/recepcion/pdf/{solicitud}', [SolicitudController::class, 'pdf_recepcion'])->name('solicitud.pdf.recepcion');
Route::get('solicitud/entrega/pdf/{solicitud}', [SolicitudController::class, 'pdf_entrega'])->name('solicitud.pdf.entrega');
Route::get('solicitud/index/mantenimiento', [SolicitudController::class, 'index'])->name('solicitud.index.mantenimiento');
Route::get('solicitud/index/baja', [SolicitudController::class, 'index'])->name('solicitud.index.baja');
Route::get('solicitud/index/peticion', [SolicitudController::class, 'index'])->name('solicitud.index.peticion');

Route::post('departamento/equipo', [EquipoController::class, 'departamento'])->name('equipo.storeDepartamento');
Route::resources([
    'departamento' => DepartamentoController::class,
    'equipo' => EquipoController::class,
    'solicitud' => SolicitudController::class,
    'tecnico' => TecnicoController::class,
]);