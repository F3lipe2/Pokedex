<?php
include("../includes/conexion.php");

// Si no hay sesión, el pokémon no es favorito
if (!isset($_SESSION['usuario_id'])) {
    echo "no";
    exit;
}

$usuario_id = (int) $_SESSION['usuario_id'];
$id         = (int) $_GET['id'];

$consulta = mysqli_query($conexion,
    "SELECT id FROM favoritos WHERE usuario_id='$usuario_id' AND pokemon_id='$id'");

if (mysqli_num_rows($consulta) > 0) {
    echo "si";
} else {
    echo "no";
}

mysqli_close($conexion);
?>