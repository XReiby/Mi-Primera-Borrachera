<?php
class Database {
    private static $conexion = NULL;

    private function __construct() {}

    public static function conectar() {
        $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        try {
            // Actualiza el usuario con el formato correcto
            self::$conexion = new PDO('pgsql:host=aws-0-sa-east-1.pooler.supabase.com;port=6543;dbname=postgres', 'postgres.ejyxavywlccaglcfobit', 'RubyCoreSystems3', $pdo_options);
        } catch (PDOException $e) {
            echo 'Error de conexión: ' . $e->getMessage();
        }
        return self::$conexion;
    }
}

// Uso de la conexión
$db = Database::conectar();
?>