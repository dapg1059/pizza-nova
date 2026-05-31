<?php
// config/database.php
// Define las constantes de conexión y crea una función para conectar.

define('DB_HOST', 'localhost');
define('DB_NAME', 'pizza_nova_db');
define('DB_USER', 'root');      // Cambia por tu usuario de MySQL
define('DB_PASS', '');          // Cambia por tu contraseña de MySQL
define('DB_CHARSET', 'utf8mb4');

/**
 * Obtiene una conexión PDO a la base de datos.
 * @return PDO
 */
function getDBConnection() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (\PDOException $e) {
            // En producción, no muestres el error directamente
            die("Error de conexión: " . $e->getMessage());
            // error_log($e->getMessage());
            // die("Error de conexión a la base de datos.");
        }
    }
    return $pdo;
}
?>