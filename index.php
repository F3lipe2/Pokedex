<?php
// Proteger la página: solo usuarios con sesión activa
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: paginas/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .user-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            padding: 6px 0 10px;
            font-size: 0.88rem;
            color: #555;
        }
        .user-bar__welcome strong { color: #c0392b; }
        .user-bar__logout {
            display: inline-block;
            padding: 4px 12px;
            border: 1px solid #c0392b;
            border-radius: 20px;
            color: #c0392b;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 600;
            transition: background 0.2s, color 0.2s;
        }
        .user-bar__logout:hover {
            background: #c0392b;
            color: #fff;
        }
    </style>
</head>

<body>

    <!-- barra arriba -->
    <div class="user-bar">
        <span class="user-bar__welcome">👤 Hola, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
        <a href="paginas/logout.php" id="btn-logout" class="user-bar__logout">Cerrar sesión</a>
    </div>

    <h1>Pokédex</h1>
    <p>Busca un Pokémon por nombre o número, o explora por tipo</p>

    <hr>

    <!-- buscar -->
    <div class="busqueda-row">
        <input type="text" id="input-pokemon" placeholder="Ej: pikachu, 25, charizard...">
        <button id="btn-buscar">Buscar</button>
        <a href="paginas/favoritosh.php">
            <button type="button" id="btn-favoritos" title="Ver mis Pokémon favoritos">Mis Favoritos</button>
        </a>
    </div>

    <!-- tipos -->
    <div id="seccion-tipos">
        <p class="tipos-titulo">Explorar por tipo:</p>
        <div id="contenedor-tipos">
            <!-- Se genera con JavaScript -->
        </div>
    </div>

    <hr>

    <!-- mensajes -->
    <p id="msg-cargando" style="display:none">Cargando...</p>
    <p id="msg-error" style="display:none;"></p>

    <!-- resultados -->
    <div id="panel-tipo" style="display:none">
        <h2 id="titulo-tipo"></h2>
        <div id="lista-por-generacion">
            <!-- Se rellena con JavaScript -->
        </div>
    </div>

    <!-- detalle -->
    <div id="tarjeta" style="display:none">

        <!-- Botón favorito (solo visual por ahora) -->
        <button id="btn-favorito" class="btn-corazon" title="Favorito">♡</button>

        <h2 id="nombre-pokemon"></h2>
        <p id="numero-pokemon"></p>

        <img id="sprite-pokemon" src="" alt="Sprite del Pokémon" width="150">

        <p><strong>Tipo(s):</strong> <span id="tipos-pokemon"></span></p>
        <p><strong>Altura:</strong> <span id="altura-pokemon"></span></p>
        <p><strong>Peso:</strong> <span id="peso-pokemon"></span></p>
        <p><strong>Habilidad:</strong> <span id="habilidad-pokemon"></span></p>

        <h3>Estadísticas base</h3>
        <table id="tabla-stats">
            <thead>
                <tr>
                    <th>Estadística</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody id="stats-body"></tbody>
        </table>

    </div>

    <script src="js/script.js"></script>

</body>

</html>