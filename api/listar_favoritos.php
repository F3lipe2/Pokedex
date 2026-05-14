<?php
include("../includes/conexion.php");

// Solo usuarios autenticados
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode([]);
    exit;
}

$usuario_id = (int) $_SESSION['usuario_id'];

$sql       = "SELECT * FROM favoritos WHERE usuario_id='$usuario_id' ORDER BY pokemon_id ASC";
$resultado = mysqli_query($conexion, $sql);

$datos = [];
while ($fila = mysqli_fetch_assoc($resultado)) {
    $datos[] = $fila;
}

Logger::info("Favoritos listados — total:" . count($datos));

echo json_encode($datos);

mysqli_close($conexion);
?>