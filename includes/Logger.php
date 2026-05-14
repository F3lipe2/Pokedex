<?php

class Logger {

    // Ruta y nombre del archivo de log
    private static string $logFile = '';

    private static function initPath() {
        if (self::$logFile === '') {
            self::$logFile = dirname(__DIR__) . '/logs/app.log';
        }
    }


    // Niveles disponibles
    const INFO    = 'INFO';
    const WARNING = 'WARNING';
    const ERROR   = 'ERROR';

    /**
     * Escribe una línea en el log.
     *
     * @param string $nivel   Nivel del log (INFO, WARNING, ERROR)
     * @param string $mensaje Descripción del evento
     */
    public static function log(string $nivel, string $mensaje): void {
        self::initPath();
        // Crear el directorio logs/ si todavía no existe
        $dir = dirname(self::$logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);

            // Bloquear acceso web al directorio
            file_put_contents($dir . '/.htaccess', "Deny from all\n");
        }

        // Detectar qué script disparó el log
        $origen = basename(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file'] ?? 'desconocido');

        // Usuario en sesión (si existe)
        $usuario = $_SESSION['username'] ?? 'anónimo';

        // Armar la línea de log
        $fecha  = date('Y-m-d H:i:s');
        $linea  = "[$fecha] [$nivel] [usuario:$usuario] [script:$origen] $mensaje" . PHP_EOL;

        // Escribir al archivo (append)
        file_put_contents(self::$logFile, $linea, FILE_APPEND | LOCK_EX);
    }

    // Atajos por nivel
    public static function info(string $mensaje): void    { self::log(self::INFO,    $mensaje); }
    public static function warning(string $mensaje): void { self::log(self::WARNING, $mensaje); }
    public static function error(string $mensaje): void   { self::log(self::ERROR,   $mensaje); }
}
