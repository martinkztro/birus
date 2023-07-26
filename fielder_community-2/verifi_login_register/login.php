<?php
session_start(); // Iniciar la sesión (asegúrate de llamar a esto antes de cualquier salida al navegador)

include '../DB/conexion.php';

// Habilitar el modo de depuración y mostrar errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email_sesion = $_POST['email'];
    $password_sesion = $_POST['password'];

    // Limpiar y escapar los datos de entrada
    $email_sesion = mysqli_real_escape_string($conn, trim($email_sesion));
    $password_sesion = mysqli_real_escape_string($conn, trim($password_sesion));

    // Obtener la contraseña hasheada y la salt almacenadas en la base de datos
    $sql = "SELECT password, salt FROM users WHERE email = '$email_sesion'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];
        $salt = $row['salt'];

        // Hashear la contraseña ingresada junto con la salt recuperada de la base de datos
        $password_with_salt = hash('sha256', $password_sesion . $salt);

        // Verificar la contraseña ingresada utilizando hash_equals()
        if (hash_equals($hashedPassword, $password_with_salt)) {
            // Las credenciales son válidas, el usuario está autenticado
            if ($email_sesion == 'Admin_M@gmail.com' || $email_sesion == 'Admin_R@gmail.com' || $email_sesion == 'Admin_J@gmail.com') {
                $_SESSION['email'] = $email_sesion;
                header('Location: ../adminpage/index.php');
                exit();
            }

            $_SESSION['email'] = $email_sesion; // Almacenar el correo electrónico en la sesión

            // Puedes realizar acciones adicionales, como redirigir a una página de inicio
            header('Location: ../campo.html');
            exit();
        } else {
            // Las credenciales son inválidas, mostrar un mensaje de error
            echo "<script>alert('CREDENCIALES INVÁLIDAS'); window.location.href = '../login/index.html';</script>";
            exit();
        }
    } else {
        // Usuario no encontrado
        echo "<script>alert('USUARIO NO ENCONTRADO'); window.location.href = '../login/index.html';</script>";
        exit();
    }
}

// Si se accede a este archivo directamente sin enviar el formulario, redirigir a una página de error
header('Location: ../info.html');
exit();
?>
