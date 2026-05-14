<?php
include("../includes/conexion.php");

// Solo usuarios autenticados pueden gestionar favoritos
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo "no_sesion";
    exit;
}

$usuario_id = (int) $_SESSION['usuario_id'];
$id         = (int) $_POST['id'];
$nombre     = mysqli_real_escape_string($conexion, $_POST['nombre']);
$imagen     = mysqli_real_escape_string($conexion, $_POST['imagen']);

// Verificar si ya es favorito de este usuario
$consulta = mysqli_query($conexion,
    "SELECT id FROM favoritos WHERE usuario_id='$usuario_id' AND pokemon_id='$id'");

if (mysqli_num_rows($consulta) > 0) {

    // Eliminar de favoritos
    mysqli_query($conexion,
        "DELETE FROM favoritos WHERE usuario_id='$usuario_id' AND pokemon_id='$id'");

    Logger::info("Favorito eliminado — pokemon_id:$id nombre:$nombre");
    echo "eliminado";

} else {

    // Agregar a favoritos
    mysqli_query($conexion,
        "INSERT INTO favoritos (usuario_id, pokemon_id, nombre, imagen)
         VALUES ('$usuario_id', '$id', '$nombre', '$imagen')");

    Logger::info("Favorito guardado — pokemon_id:$id nombre:$nombre");
    echo "guardado";
}

mysqli_close($conexion);
?>