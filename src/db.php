<?php

class DB {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;

    public function __construct() {
        // Estos valores permiten que la API funcione en local y también en Render.
        // Si Render tiene variables de entorno, usará esas.
        // Si no las tiene, usará los datos locales de XAMPP.
        $this->host = getenv('DB_HOST') ?: "localhost";
        $this->port = getenv('DB_PORT') ?: "3306";
        $this->db_name = getenv('DB_NAME') ?: "salud_cr23036";
        $this->username = getenv('DB_USER') ?: "root";
        $this->password = getenv('DB_PASSWORD') ?: "";
    }

    public function connect() {
        // Se crea la cadena de conexión para MySQL usando PDO
        $mysql_connect_str = "mysql:host=$this->host;port=$this->port;dbname=$this->db_name;charset=utf8mb4";

        $dbConnection = new PDO($mysql_connect_str, $this->username, $this->password);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $dbConnection;
    }
}