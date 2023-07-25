<?php
$host = 'localhost';
$user = 'root';
$password = 'Jazziel1218*';
$database = 'FielderCommunity';
$port = 3306;

// Crear una conexión a la base de datos
$conn = new mysqli($host, $user, $password, $database, $port);

// Verificar si la conexión es exitosa
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
} else {
    echo "Conexión exitosa";
}
?>
