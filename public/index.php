<?php 
require_once __DIR__ . '/../includes/app.php';

use MVC\Router;

use Controllers\RecetaController;
use Controllers\IngredienteController;
use Controllers\RecetaIngredienteController;

$router = new Router();

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


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();