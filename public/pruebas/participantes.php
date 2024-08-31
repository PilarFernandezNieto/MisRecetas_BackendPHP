<?php
require_once __DIR__ . '/../../includes/app.php';
// http://localhost:3000/pruebas/participantes.php

$data = file_get_contents("../../data/participantes.json");
$participantes = json_decode($data, true);

echo json_encode($participantes);