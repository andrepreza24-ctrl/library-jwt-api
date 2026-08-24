}<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "API E-Commerce Segura",
    description: "Documentación interactiva de endpoints con JWT y Stripe"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Servidor Local Docker"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class AuthController extends Controller
{
    // ... tu código de AuthController sin cambios ...
}