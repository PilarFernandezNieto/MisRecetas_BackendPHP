<?php 
require_once __DIR__ . '/../includes/app.php';

use MVC\Router;

use Controllers\AuthController;
use Controllers\RecetaController;
use Controllers\IngredienteController;
use Controllers\RecetaIngredienteController;

$router = new Router();

$router->get('/', function() {
    echo "Bienvenido a la API. Navega a /api/recetas o /api/auth para acceder a los recursos.";
});

$router->get('/404', function() {
    http_response_code(404);
    echo "Ruta no encontrada";
});


$router->get("/api/recetas", [RecetaController::class, "index"]);
$router->post("/api/recetas",[RecetaController::class, "crear"] );
$router->get("/api/recetas/ver-receta", [RecetaController::class, "getById"]);
$router->put("/api/recetas/actualizar/:id", [RecetaController::class, "actualizar"]);
$router->post("/api/recetas/eliminar/:id", [RecetaController::class, "eliminar"]);

$router->get("/api/ingredientes", [IngredienteController::class, "index"]);
$router->post("/api/ingredientes",[IngredienteController::class, "crear"] );
$router->get("/api/ingredientes/:id",[IngredienteController::class, "getById"] );
$router->put("/api/ingredientes/actualizar/:id", [IngredienteController::class, "actualizar"]);
$router->delete("/api/ingredientes/eliminar/:id", [IngredienteController::class, "eliminar"]);

$router->get("/api/receta-ingrediente", [RecetaIngredienteController::class, "index"]);
$router->post("/api/receta-ingrediente",[RecetaIngredienteController::class, "crear"] );

// Autenticación y registro de usuarios /api/auth
$router->post("/api/auth/registro", [AuthController::class, "registro"]);
$router->get('/api/auth/confirmar-cuenta/:token', [AuthController::class, 'confirmar']);
$router->post('/api/auth/login', [AuthController::class, 'login']);




// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();