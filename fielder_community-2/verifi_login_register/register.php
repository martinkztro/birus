<?php
session_start(); // Iniciar la sesión
include '../DB/conexion.php';

// Verificar si se ha enviado el formulario de registro
if (
    isset($_POST['nombre_usuario']) && !empty($_POST['nombre_usuario']) &&
    isset($_POST['email']) && !empty($_POST['email']) &&
    isset($_POST['password']) && !empty($_POST['password']) &&
    isset($_POST['confirm_password']) && !empty($_POST['confirm_password'])
) {
    $nombre_usuario = $_POST['nombre_usuario'];
    $email_veri = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $fechaActual = date("Y-m-d");

    // Verificar si las contraseñas coinciden
    if ($password !== $confirm_password) {
        echo "<script>alert('Las contraseñas no coinciden'); window.location.href = '../login/index.html/#register';</script>";
        exit();
    }

    // Generar la salt única para cada usuario
    $salt = hash('sha256', uniqid());

    // Concatenar la salt a la contraseña antes de hashearla
    $hashedPassword = hash('sha256', $password . $salt);

    // Verificar si el correo electrónico y el nombre de usuario existen en la base de datos
    $verificar_sql = "SELECT * FROM users WHERE email = '$email_veri' OR nombre_usuario = '$nombre_usuario'";
    $result = $conn->query($verificar_sql);

    if ($result->num_rows > 0) {
        echo "<script>alert('El nombre de usuario o correo electrónico ya existen'); window.location.href = '../login/index.html/#register';</script>";
        exit();
    } else {
        // Aquí puedes realizar la lógica de registro, como almacenar los datos en una base de datos
        $sql = "INSERT INTO users (nombre_usuario, email, password, salt, created_at)
            VALUES ('$nombre_usuario', '$email_veri', '$hashedPassword', '$salt', '$fechaActual')";

        if ($conn->query($sql) === TRUE) {
            // Registro exitoso
            header('Location: ../campo.html');
            exit();
        } else {
            // Ocurrió un error durante la inserción
            echo "<script>alert('ERROR DURANTE EL REGISTRO'); window.location.href = '../info.html';</script>";
            exit();
        }
    }
} else {
    echo "<script>alert('CREDENCIALES INVÁLIDAS'); window.location.href = '../info.html';</script>";
    $conn->error;
}

// Si se accede a este archivo directamente sin enviar el formulario, redirigir a una página de error
// header('Location: error.html');
// exit();
?>
