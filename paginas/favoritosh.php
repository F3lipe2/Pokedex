<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex — Mis Favoritos</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        /* Tipos */

        .nav-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #c0392b;
            font-weight: bold;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 14px;
            transition: opacity 0.2s;
        }
        .nav-back:hover { opacity: 0.75; }

        /* Resultados */
        #contenedor-favoritos {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 20px;
        }

        .card-favorito {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 16px 12px;
            text-align: center;
            width: 130px;
            cursor: pointer;
            position: relative;
            transition: transform 0.15s, box-shadow 0.15s, border-color 0.2s;
        }

        .card-favorito:hover {
            transform: translateY(-4px) scale(1.04);
            box-shadow: 0 6px 18px rgba(192, 57, 43, 0.18);
            border-color: #c0392b;
        }

        .card-favorito img {
            image-rendering: pixelated;
            width: 96px;
            height: 96px;
            object-fit: contain;
        }

        .card-favorito h3 {
            font-size: 13px;
            margin: 8px 0 4px;
            text-transform: capitalize;
            color: #333;
        }

        .card-favorito .num-pokemon {
            font-size: 11px;
            color: #888;
        }

        /* Botón eliminar */
        .btn-eliminar {
            position: absolute;
            top: 6px;
            right: 6px;
            background: none;
            border: 1px solid #ccc;
            border-radius: 50%;
            font-size: 14px;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #e74c3c;
            padding: 0;
            margin: 0;
            transition: background-color 0.2s, border-color 0.2s, transform 0.15s;
        }

        .btn-eliminar:hover {
            background-color: #fdecea;
            border-color: #e74c3c;
            transform: scale(1.15);
        }

        /* Busqueda */
        .msg-vacio {
            color: #888;
            font-size: 15px;
            margin-top: 30px;
        }

        /* Contador */
        #contador-favoritos {
            font-size: 14px;
            color: #555;
            margin-top: 4px;
        }
    </style>
</head>

<body>

    <!-- nav -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
        <a href="../index.php" class="nav-back">&#8592; Volver a la Pokédex</a>
        <span style="font-size:0.85rem; color:#888;">
            👤 <?= htmlspecialchars($_SESSION['username']) ?>
            &nbsp;·&nbsp;
            <a href="logout.php" style="color:#c0392b; font-weight:600; text-decoration:none;">Cerrar sesión</a>
        </span>
    </div>

    <h1>❤️ Mis Favoritos</h1>
    <p id="contador-favoritos">Cargando...</p>

    <hr>

    <!-- tarjetas -->
    <div id="contenedor-favoritos"></div>

    <script src="../js/favoritos.js"></script>

</body>

</html>
