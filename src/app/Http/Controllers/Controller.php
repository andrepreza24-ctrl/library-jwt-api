<?php
namespace App\Http\Controllers;
/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="API E-Commerce Segura",
 *      description="Documentación interactiva de endpoints con autenticación JWT y procesamiento de pagos Stripe.",
 *      @OA\Contact(
 *          email="soporte@tienda.com"
 *      )
 * )
 *
 * @OA\Server(
 *      url="http://localhost:8000",
 *      description="Servidor Local Docker"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 *      description="Ingrese el token JWT obtenido al iniciar sesión (ejemplo: Bearer {token})"
 * )
 */
abstract class Controller
{
    //
}