<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Departamento;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Equipo as EquipoRequests;

class EquipoController extends Controller
{

    protected $equipo;
    public function __construct(Equipo $equipo){
        $this->equipo = $equipo;
    }
    
    public function index()
    {
        if (auth()->user()->rol == 'departamento') {
            $email = auth()->user()->email;
            $departamento_id = Departamento::where('correo', $email)->value('id');
            $equipo = Equipo::where('departamento_id', $departamento_id)->get();
            $departamento = Departamento::find($departamento_id);
        }else {
            $equipo = Equipo::all();
            $departamento = Departamento::all();
        }
        return view('templates.content.administrador.equipo.index', compact('equipo','departamento'));
    }
    
    public function create()
    {
        //
    }
    
    public function store(EquipoRequests $request)
    {
        $equipo = $this->equipo->create($request->all());
        return $this->index();
    }
    
    public function departamento(EquipoRequests $request)
    {
        $equipo = $this->equipo->create($request->all());
        
        return redirect(route('departamento.show', $request->departamento_id));
    }

    public function show($equipo)
    {
        $solicitud = Solicitud::where('equipo_id', $equipo)->orderBy('estatus', 'desc')->get();
        $equipo = Equipo::find($equipo);
        return view('templates.content.administrador.equipo.show', compact('equipo', 'solicitud'));
    }
    
    public function edit(Equipo $equipo)
    {
        //
    }
    
    public function update(EquipoRequests $request, $equipo)
    {
        $id = $equipo;
        $equipo = Equipo::find($id);
        $equipo->update($request->all());
        return $this->show($id);
    }
    
    public function destroy(Equipo $equipo)
    {
        //
    }
}
