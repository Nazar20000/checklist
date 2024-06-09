<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);

    $errors = [];

    // Валидация имени
    if (empty($name)) {
        $errors[] = "Имя обязательно.";
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $name)) {
        $errors[] = "Имя может содержать только буквы, цифры и символы подчеркивания.";
    }

    // Валидация пароля
    if (empty($password)) {
        $errors[] = "Пароль обязателен.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Пароль должен быть не менее 6 символов.";
    }

    // Валидация email
    if (empty($email)) {
        $errors[] = "Email обязателен.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Неверный формат email.";
    }

    // Проверка уникальности email
    $sql = "SELECT id FROM client WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Этот email уже зарегистрирован.";
        }
        $stmt->close();
    } else {
        $errors[] = "Ошибка подготовки запроса: " . $conn->error;
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO client (name, password, email) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $name, $hashed_password, $email);
            if ($stmt->execute()) {
                header("Location: vhod.php");
                exit();
            } else {
                $errors[] = "Ошибка: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors[] = "Ошибка подготовки запроса: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form action="index.php" method="post">
                <input type="text" name="name" placeholder="Имя" class="input" required><br>
                <input type="password" name="password" placeholder="Пароль" class="input" required><br>
                <input type="email" name="email" placeholder="Email" class="input" required><br>
                <button type="submit" class="button">Регистрация</button>
                <a href="vhod.php" class="link">Войти</a>
            </form>
            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
