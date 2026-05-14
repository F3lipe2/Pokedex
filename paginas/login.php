<?php
session_start();
require_once '../includes/conexion.php';

// redirect si ya hay sesion
if (isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Por favor completa todos los campos.';
    } else {
        // buscar user
        $stmt = $conexion->prepare("SELECT id, username, password FROM usuarios WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario   = $resultado->fetch_assoc();
        $stmt->close();

        if ($usuario && password_verify($password, $usuario['password'])) {
            // todo ok
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['username']   = $usuario['username'];
            Logger::info("Login exitoso para: $username");
            header('Location: ../index.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
            Logger::warning("Intento de login fallido para: $username");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex — Iniciar sesión</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            min-height: 100vh !important;
            background-color: #fcfcfc !important;
            margin: 0 !important;
            max-width: none !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .login-card {
            background: #fff;
            padding: 3rem;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border-radius: 8px;
            border-top: 5px solid #c0392b;
            text-align: center;
        }

        .login-card h1 {
            font-size: 2rem;
            margin-bottom: 2rem;
            color: #c0392b;
            font-weight: 300;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .login-card input {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #eee;
            border-radius: 4px;
            font-size: 15px;
            background-color: #f9f9f9;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .login-card input:focus {
            outline: none;
            border-color: #c0392b;
            background-color: #fff;
            box-shadow: 0 0 8px rgba(192, 57, 43, 0.1);
        }

        .login-card button {
            width: 100%;
            padding: 12px;
            background-color: #c0392b;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .login-card button:hover {
            background-color: #a93226;
        }

        .msg-error {
            color: #c0392b;
            font-size: 14px;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #fff5f5;
            border-left: 3px solid #c0392b;
            text-align: left;
        }

        .footer-links {
            margin-top: 2rem;
            font-size: 14px;
            color: #777;
        }

        .footer-links a {
            color: #c0392b;
            text-decoration: none;
            font-weight: 600;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h1>Iniciar Sesión</h1>
        
        <?php if ($error): ?>
            <div class="msg-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input
                type="text"
                name="username"
                placeholder="Nombre de usuario"
                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                required>

            <input
                type="password"
                name="password"
                placeholder="Contraseña"
                required>

            <button type="submit">Acceder</button>
        </form>

            <div class="footer-links">
                ¿Aún no tienes cuenta? <a href="registro.php">Regístrate ahora</a>
            </div>
    </div>

</body>
</html>

