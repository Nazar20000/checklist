<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $password = $_POST['password'];

    $sql = "SELECT id, name, password FROM client WHERE name = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                header("Location: main.php");
                exit();
            } else {
                echo "Неправильный пароль.";
            }
        } else {
            echo "Пользователь не найден.";
        }

        $stmt->close();
    } else {
        echo "Ошибка подготовки: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form action="vhod.php" method="post">
                <input type="text" name="name" placeholder="Имя" class="input" required><br>
                <input type="password" name="password" placeholder="Пароль" class="input" required><br>
                <button type="submit" class="button">Войти</button>
                <a href="index.php" class="link">Регистрация</a>
            </form>
        </div>
    </div>
</body>
</html>
