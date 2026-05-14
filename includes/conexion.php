<?php

// Iniciar sesión una sola vez para toda la aplicación
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar el sistema de logs
require_once __DIR__ . '/Logger.php';

// Conectar a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "pokedexbd");

if (!$conexion) {
    Logger::error("Error de conexión a la BD: " . mysqli_connect_error());
    die("Error de conexión: " . mysqli_connect_error());
}

?>