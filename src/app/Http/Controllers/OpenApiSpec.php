<?php
namespace App\Http\Controllers;

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

// --- AUTENTICACIÓN ---
#[OA\Post(
    path: "/api/register",
    summary: "Registro de usuario",
    tags: ["Autenticación"],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "email", "password"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Cliente Ejemplo"),
                new OA\Property(property: "email", type: "string", example: "cliente@email.com"),
                new OA\Property(property: "password", type: "string", example: "123456")
            ]
        )
    ),
    responses: [new OA\Response(response: 201, description: "Usuario registrado con éxito")]
)]
#[OA\Post(
    path: "/api/login",
    summary: "Iniciar sesión",
    tags: ["Autenticación"],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email", "password"],
            properties: [
                new OA\Property(property: "email", type: "string", example: "cliente@email.com"),
                new OA\Property(property: "password", type: "string", example: "123456")
            ]
        )
    ),
    responses: [new OA\Response(response: 200, description: "Token JWT generado")]
)]

// --- PRODUCTOS ---
#[OA\Get(
    path: "/api/products",
    summary: "Obtener catálogo de productos",
    tags: ["Productos"],
    responses: [new OA\Response(response: 200, description: "Lista de productos obtenida")]
)]

// --- ÓRDENES Y STRIPE ---
#[OA\Post(
    path: "/api/orders",
    summary: "Crear orden y procesar pago con Stripe",
    tags: ["Órdenes"],
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["items", "stripe_token"],
            properties: [
                new OA\Property(
                    property: "items",
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "product_id", type: "integer", example: 1),
                            new OA\Property(property: "quantity", type: "integer", example: 2)
                        ]
                    )
                ),
                new OA\Property(property: "stripe_token", type: "string", example: "tok_visa")
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: "Orden creada y pago procesado en Stripe"),
        new OA\Response(response: 401, description: "No autorizado (JWT requerido)")
    ]
)]
class OpenApiSpec
{
}