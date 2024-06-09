<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: vhod.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$checklist_id = $_GET['id'];

$sql = "SELECT title FROM checklist WHERE id = ? AND user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $checklist_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($title);
    if (!$stmt->fetch()) {
        header("Location: main.php");
        exit();
    }
    $stmt->close();
} else {
    echo "Ошибка подготовки: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['content'])) {
    $content = $_POST['content'];
    $sql = "INSERT INTO checklist_item (checklist_id, content) VALUES (?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("is", $checklist_id, $content);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Ошибка подготовки: " . $conn->error;
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $sql = "DELETE FROM checklist_item WHERE id = ? AND checklist_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $delete_id, $checklist_id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Ошибка подготовки: " . $conn->error;
    }
}

$sql = "SELECT id, content, is_completed FROM checklist_item WHERE checklist_id = ?";
$items = [];
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $checklist_id);
    $stmt->execute();
    $stmt->bind_result($item_id, $item_content, $is_completed);
    while ($stmt->fetch()) {
        $items[] = ['id' => $item_id, 'content' => $item_content, 'is_completed' => $is_completed];
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
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="css/checklist.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="main.php" class="back-button">Назад</a>
            <h1><?php echo htmlspecialchars($title); ?></h1>
        </div>
        <div class="content">
            <form action="checklist.php?id=<?php echo $checklist_id; ?>" method="post">
                <input type="text" name="content" placeholder="Новая задача" required>
                <button type="submit">Добавить</button>
            </form>
            <ul>
                <?php foreach ($items as $item): ?>
                    <li>
                        <?php echo htmlspecialchars($item['content']); ?> 
                        <?php if ($item['is_completed']): ?>
                            (Выполнено)
                        <?php endif; ?>
                        <form action="checklist.php?id=<?php echo $checklist_id; ?>" method="post" class="delete-form">
                            <input type="hidden" name="delete_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" class="delete-button">Удалить</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>
