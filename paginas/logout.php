<?php
session_start();
require_once '../includes/Logger.php';

// Registrar el cierre de sesión antes de destruirla
if (isset($_SESSION['username'])) {
    Logger::info("Sesión cerrada para: " . $_SESSION['username']);
}

// Destruir todos los datos de sesión
$_SESSION = [];
session_destroy();

// Redirigir al login
header('Location: login.php');
exit;
