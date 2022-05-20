<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Equipo;
use App\Models\Solicitud;
use App\Http\Requests\Departamento as DepartamentoRequests;

class DepartamentoController extends Controller
{

    protected $departamento;
    public function __construct(Departamento $departamento){
        $this->departamento = $departamento;
    }
    
    public function index()
    {
        $departamento = Departamento::all();
        return view('templates.content.departamento.index', compact('departamento'));
    }
    
    public function create()
    {
        //
    }
    
    public function store(DepartamentoRequests $request)
    {
        $departamento = $this->departamento->create($request->all());
        return $this->index();
    }
    
    public function show($departamento)
    {
        $solicitud = Solicitud::where('departamento_id', $departamento)->get();
        $equipo = Equipo::where('departamento_id', $departamento)->get();
        $departamento = Departamento::find($departamento);
        return view('templates.content.departamento.show', compact('departamento','solicitud','equipo'));
    }
    
    public function edit(Departamento $departamento)
    {
        //
    }
    
    public function update(DepartamentoRequests $request, $departamento)
    {
        $id = $departamento;
        $departamento = Departamento::find($id);
        $departamento->update($request->all());
        return $this->show($id);
    }
    
    public function destroy(Departamento $departamento)
    {
        //
    }
}