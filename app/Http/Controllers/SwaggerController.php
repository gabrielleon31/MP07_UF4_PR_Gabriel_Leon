<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API de Productes",
 *     description="Documentació de l'API per gestionar productes amb rols d'usuari i autenticació via Sanctum."
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Servidor local"
 * )
 */
class SwaggerController extends Controller
{
    // Este archivo existe solo para que Swagger pueda escanear las anotaciones globales
}
