<?php

namespace App\Http\Controllers;

class UtilityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get available lead sources.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLeadSources()
    {
        $sources = [
            ['value' => 'Web', 'label' => 'Sitio Web'],
            ['value' => 'Referido', 'label' => 'Referido'],
            ['value' => 'Visita en concesionario', 'label' => 'Visita en concesionario'],
            ['value' => 'Teléfono', 'label' => 'Llamada telefónica'],
            ['value' => 'Email', 'label' => 'Correo electrónico'],
            ['value' => 'Redes sociales', 'label' => 'Redes sociales'],
            ['value' => 'Anuncio', 'label' => 'Anuncio publicitario'],
            ['value' => 'Evento', 'label' => 'Evento o feria'],
            ['value' => 'Otro', 'label' => 'Otro'],
        ];
        
        return response()->json($sources);
    }

    /**
     * Get available interaction types.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInteractionTypes()
    {
        $types = [
            ['value' => 'Llamada', 'label' => 'Llamada telefónica'],
            ['value' => 'Email', 'label' => 'Correo electrónico'],
            ['value' => 'Visita', 'label' => 'Visita al concesionario'],
            ['value' => 'Prueba', 'label' => 'Prueba de manejo'],
            ['value' => 'Cotización', 'label' => 'Cotización'],
            ['value' => 'Seguimiento', 'label' => 'Seguimiento general'],
            ['value' => 'Negociación', 'label' => 'Negociación'],
            ['value' => 'Postventa', 'label' => 'Servicio postventa'],
            ['value' => 'Otro', 'label' => 'Otro'],
        ];
        
        return response()->json($types);
    }

    /**
     * Get available interaction statuses.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInteractionStatuses()
    {
        $statuses = [
            ['value' => 'pendiente', 'label' => 'Pendiente'],
            ['value' => 'en_proceso', 'label' => 'En proceso'],
            ['value' => 'completado', 'label' => 'Completado'],
            ['value' => 'cancelado', 'label' => 'Cancelado'],
            ['value' => 'reprogramado', 'label' => 'Reprogramado'],
        ];
        
        return response()->json($statuses);
    }
}