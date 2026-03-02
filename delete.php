<?php
require_once 'config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
try {
    $sql = "
        UPDATE employees 
        SET fired = TRUE, 
            fired_date = CURRENT_DATE 
        WHERE id = :id AND fired = FALSE
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    
    if ($stmt->rowCount() > 0) {
        header('Location: index.php?message=Сотрудник уволен');
    } else {
        header('Location: index.php?error=Сотрудник не найден или уже уволен');
    }
    
} catch (PDOException $e) {
    header('Location: index.php?error=Ошибка при увольнении');
}
exit;
?>