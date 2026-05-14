<?php
session_start();
require_once '../includes/conexion.php';

// si hay sesion, fuera
if (isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$error  = '';
$exito  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username  = trim($_POST['username']  ?? '');
    $password  = $_POST['password']  ?? '';
    $password2 = $_POST['password2'] ?? '';

    // Validaciones básicas
    if ($username === '' || $password === '' || $password2 === '') {
        $error = 'Por favor completa todos los campos.';

    } elseif (strlen($username) < 3) {
        $error = 'El nombre de usuario debe tener al menos 3 caracteres.';

    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';

    } elseif ($password !== $password2) {
        $error = 'Las contraseñas no coinciden.';

    } else {
        // check si ya existe
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Ese nombre de usuario ya está en uso.';
            $stmt->close();
        } else {
            $stmt->close();

            // hash y guardar
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins  = $conexion->prepare("INSERT INTO usuarios (username, password) VALUES (?, ?)");
            $ins->bind_param('ss', $username, $hash);

            if ($ins->execute()) {
                Logger::info("Nuevo usuario registrado: $username");
                $exito = '¡Cuenta creada! Ya puedes <a href="login.php">iniciar sesión</a>.';
            } else {
                Logger::error("Error al registrar usuario: $username — " . $conexion->error);
                $error = 'Ocurrió un error al crear tu cuenta. Intenta de nuevo.';
            }

            $ins->close();
        }
    }

    mysqli_close($conexion);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex — Crear cuenta</title>
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

        .msg-exito {
            color: #27ae60;
            font-size: 14px;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f0fff4;
            border-left: 3px solid #27ae60;
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
        <h1>Crear Cuenta</h1>
        
        <?php if ($error): ?>
            <div class="msg-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="msg-exito"><?= $exito ?></div>
        <?php else: ?>
        <form method="POST" action="registro.php">
            <input
                type="text"
                name="username"
                placeholder="Nombre de usuario"
                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                required>

            <input
                type="password"
                name="password"
                placeholder="Contraseña (mín. 6 caracteres)"
                required>

            <input
                type="password"
                name="password2"
                placeholder="Repite la contraseña"
                required>

            <button type="submit">Registrarse</button>
        </form>
        <?php endif; ?>

        <div class="footer-links">
            ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a>
        </div>
    </div>

</body>
</html>

