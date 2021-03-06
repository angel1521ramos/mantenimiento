<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Departamento;
use App\Models\Tecnico;
use App\Models\Equipo;
use App\Http\Requests\Solicitud as SolicitudRequests;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Support\Facades\Auth;

class SolicitudController extends Controller
{

    protected $solicitud;
    public function __construct(Solicitud $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    public function index()
    {

        if (auth()->user()->rol == 'departamento') {
            $email = auth()->user()->email;
            $departamento_id = Departamento::where('correo', $email)->value('id');
            $departamento = Departamento::find($departamento_id);
            $equipo = Equipo::where('departamento_id', $departamento_id)->orderBy('departamento_id', 'asc')->get();

            if (request()->is('solicitud/index/mantenimiento')) {
                $solicitud = Solicitud::where('tipo', 'MANTENIMIENTO')->where('departamento_id', $departamento_id)->orderBy('estatus', 'desc')->get();
            } elseif (request()->is('solicitud/index/peticion')) {
                $solicitud = Solicitud::where('tipo', 'PETICION')->where('departamento_id', $departamento_id)->orderBy('estatus', 'desc')->get();
            } elseif (request()->is('solicitud/index/baja')) {
                $solicitud = Solicitud::where('tipo', 'BAJA')->where('departamento_id', $departamento_id)->orderBy('estatus', 'desc')->get();
            } else {
                $solicitud = Solicitud::where('departamento_id', $departamento_id)->orderBy('estatus', 'desc')->get();
            }
        } else {

            if (request()->is('solicitud/index/mantenimiento')) {
                $solicitud = Solicitud::where('tipo', 'MANTENIMIENTO')->orderBy('estatus', 'desc')->get();
            } elseif (request()->is('solicitud/index/peticion')) {
                $solicitud = Solicitud::where('tipo', 'PETICION')->orderBy('estatus', 'desc')->get();
            } elseif (request()->is('solicitud/index/baja')) {
                $solicitud = Solicitud::where('tipo', 'BAJA')->orderBy('estatus', 'desc')->get();
            } else {
                $solicitud = Solicitud::orderBy('estatus', 'desc')->get();
            }
            $departamento = Departamento::orderBy('id', 'asc')->get();
            $equipo = Equipo::orderBy('departamento_id', 'asc')->get();
        }

        return view('templates.content.administrador.solicitud.index', compact('departamento', 'solicitud', 'equipo'));
    }

    public function store(SolicitudRequests $request)
    {

        $departamento_id = Equipo::where('id', $request->equipo_id)->value('departamento_id');
        $departamento = Departamento::find($departamento_id);

        $Solicitud = Solicitud::create([
            'equipo_id' => $request->equipo_id,
            'departamento_id' => $departamento_id,
            'identificador' => $request->identificador,
            'observacion' => $request->observacion,
            'tipo' => $request->tipo,
            'estatus' => $request->estatus,
        ]);

        if ($request->tecnico_id !== null) {
            Solicitud::where('id', $Solicitud->id)->update([
                'tecnico_id' => $request->tecnico_id
            ]);
        }

        if ($request->tipo == 'BAJA' && $request->estatus == 'FINALIZADO') {
            Equipo::where('id', $request->equipo_id)->update([
                'estatus' => 'BAJA'
            ]);
        } else {
            Equipo::where('id', $request->equipo_id)->update([
                'estatus' => 'ACTIVO'
            ]);
        }

        return back();
    }

    public function peticion_store(SolicitudRequests $request)
    {
        $departamento = Departamento::find($request->departamento_id);

        $Solicitud = Solicitud::create([
            'departamento_id' => $request->departamento_id,
            'identificador' => $request->identificador,
            'observacion' => $request->observacion,
            'tipo' => $request->tipo,
            'estatus' => $request->estatus,
            'peticion_equipo' => $request->peticion_equipo,
        ]);

        if ($request->tecnico_id !== null) {
            Solicitud::where('id', $Solicitud->id)->update([
                'tecnico_id' => $request->tecnico_id
            ]);
        }

        return back();
    }

    public function show($solicitud)
    {
        $tecnico = Tecnico::all();
        $departamento = Departamento::all();
        $departamento_id = Solicitud::where('id', $solicitud)->value('departamento_id');
        $equipo = Equipo::where('departamento_id', $departamento_id)->get();
        $solicitud = Solicitud::find($solicitud);
        return view('templates.content.administrador.solicitud.show', compact('departamento', 'equipo', 'solicitud', 'tecnico'));
    }

    public function update(SolicitudRequests $request, $solicitud)
    {
        Solicitud::where('id', $solicitud)->update([
            'identificador' => $request->identificador,
            'equipo_id' => $request->equipo_id,
            'departamento_id' => $request->departamento_id,
            'observacion' => $request->observacion,
            'tipo' => $request->tipo,
            'estatus' => $request->estatus,
            'tecnico_id' => $request->tecnico_id
        ]);

        if ($request->calificacion !== null) {
            Solicitud::where('id', $solicitud)->update([
                'calificacion' => $request->calificacion
            ]);
        }

        if ($request->tipo == 'BAJA' && $request->estatus == 'FINALIZADO') {
            Equipo::where('id', $request->equipo_id)->update([
                'estatus' => 'BAJA'
            ]);
        } else {
            Equipo::where('id', $request->equipo_id)->update([
                'estatus' => 'ACTIVO'
            ]);
        }

        return $this->show($solicitud);
    }

    public function peticion_update(SolicitudRequests $request, $solicitud)
    {
        Solicitud::where('id', $solicitud)->update([
            'identificador' => $request->identificador,
            'peticion_equipo' => $request->peticion_equipo,
            'departamento_id' => $request->departamento_id,
            'observacion' => $request->observacion,
            'tipo' => $request->tipo,
            'estatus' => $request->estatus,
            'tecnico_id' => $request->tecnico_id
        ]);

        if ($request->calificacion !== null) {
            Solicitud::where('id', $solicitud)->update([
                'calificacion' => $request->calificacion
            ]);
        }

        return $this->show($solicitud);
    }

    public function destroy(Solicitud $solicitud)
    {
        //
    }

    public function pdf_recepcion(Solicitud $solicitud)
    {
        $pdf = PDF::loadView('templates.content.administrador.solicitud.recepcion', compact('solicitud'));
        return $pdf->stream();
        /**$pdf = PDF::loadView('templates.content.administrador.solicitud.pdf');
        return $pdf->stream();**/
    }

    public function pdf_entrega(Solicitud $solicitud)
    {
        $pdf = PDF::loadView('templates.content.administrador.solicitud.entrega', compact('solicitud'));
        return $pdf->stream();
        /**$pdf = PDF::loadView('templates.content.administrador.solicitud.pdf');
        return $pdf->stream();**/
    }
}
