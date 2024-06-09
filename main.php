<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: vhod.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT name FROM client WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "Ошибка подготовки: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['title'])) {
        $title = $_POST['title'];
        $sql = "INSERT INTO checklist (user_id, title) VALUES (?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("is", $user_id, $title);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Ошибка подготовки: " . $conn->error;
        }
    } elseif (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        $sql = "DELETE FROM checklist WHERE id = ? AND user_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ii", $delete_id, $user_id);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Ошибка подготовки: " . $conn->error;
        }
    }
}

$sql = "SELECT id, title FROM checklist WHERE user_id = ?";
$checklists = [];
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($checklist_id, $checklist_title);
    while ($stmt->fetch()) {
        $checklists[] = ['id' => $checklist_id, 'title' => $checklist_title];
    }
    $stmt->close();
} else {
    echo "Ошибка подготовки: " . $conn->error;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="name_user">
                <h3>Добро пожаловать, <?php echo htmlspecialchars($name); ?>!</h3><br><br>
            </div>
            <div class="menu">
                <a href="main.php" class="menu-item">Главная</a>
                <a href="#" class="menu-item">Информация</a>
                <a href="logout.php" class="menu-item">Выход</a>
            </div>
        </div>
        <div class="content">
            <h1>Ваши чек-листы</h1>
            <form action="main.php" method="post">
                <input type="text" name="title" placeholder="Название чек-листа" required> <br><br>
                <button type="submit">Создать</button>
            </form>
            <ul>
                <?php foreach ($checklists as $checklist): ?>
                    <li>
                        <a href="checklist.php?id=<?php echo $checklist['id']; ?>">
                            <?php echo htmlspecialchars($checklist['title']); ?>
                        </a>
                        <form action="main.php" method="post" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $checklist['id']; ?>">
                            <button type="submit" class="delete-button">Удалить</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>
