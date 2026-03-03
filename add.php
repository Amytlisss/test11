<?php
require_once 'config/database.php';

$errors = [];
//Выпадающие списки должностей и отделов
$departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll();

$positions = $pdo->query("SELECT * FROM positions ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $last_name = trim($_POST['last_name'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $patronymic = trim($_POST['patronymic'] ?? '');
    $birth_date = $_POST['birth_date'] ?? '';
    $passport = trim($_POST['passport'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $department_id = $_POST['department_id'] ?? '';
    $position_id = $_POST['position_id'] ?? '';
    $salary = $_POST['salary'] ?? '';
    $hire_date = $_POST['hire_date'] ?? '';
    
    if (empty($last_name)) {
        $errors[] = 'Заполните поле "Фамилия"';
    }
    
    if (empty($first_name)) {
        $errors[] = 'Заполните поле "Имя"';
    }
    
    if (empty($birth_date)) {
        $errors[] = 'Заполните поле "Дата рождения"';
    }
    
    if (empty($passport)) {
        $errors[] = 'Заполните поле "Паспортные данные"';
    }
    
    if (empty($phone)) {
        $errors[] = 'Заполните поле "Телефон"';
    }
    
    if (empty($address)) {
        $errors[] = 'Заполните поле "Адрес"';
    }
    
    if (empty($department_id)) {
        $errors[] = 'Выберите отдел';
    }
    
    if (empty($position_id)) {
        $errors[] = 'Выберите должность';
    }
    
    if (empty($salary) || !is_numeric($salary) || $salary <= 0) {
        $errors[] = 'Зарплата должна быть положительным числом';
    }
    
    if (empty($hire_date)) {
        $errors[] = 'Запольните поле "Дата приема"';
    }

    if (empty($errors)) {
        try {
            $sql = "
                INSERT INTO employees (
                    last_name, first_name, patronymic, birth_date, 
                    passport, phone, address, department_id, 
                    position_id, salary, hire_date
                ) VALUES (
                    :last_name, :first_name, :patronymic, :birth_date,
                    :passport, :phone, :address, :department_id,
                    :position_id, :salary, :hire_date
                )
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'last_name' => $last_name,
                'first_name' => $first_name,
                'patronymic' => $patronymic,
                'birth_date' => $birth_date,
                'passport' => $passport,
                'phone' => $phone,
                'address' => $address,
                'department_id' => $department_id,
                'position_id' => $position_id,
                'salary' => $salary,
                'hire_date' => $hire_date
            ]);
            
            header('Location: index.php');
            exit;
            
        } catch (PDOException $e) {
            $errors[] = 'Ошибка базы данных: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление сотрудника</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://unpkg.com/imask"></script>
</head>
<body>
    <div class="container">
        <h1>Добавление нового сотрудника</h1>

        <?php if (!empty($errors)): ?>
            <div class ="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" action="add.php">
                
                <div class="form-group">
                    <label for = "last_name">Фамилия </label>
                    <input type="text" id = "last_name" name="last_name" value="<?=htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for = "first_name">Имя </label>
                    <input type="text" id = "first_name" name="first_name" value="<?=htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for = "patronymic">Отчество </label>
                    <input type="text" id = "patronymic" name="patronymic" value="<?=htmlspecialchars($_POST['patronymic'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for = "birth_date">Дата рождения </label>
                    <input type="date" id = "birth_date" name="birth_date" value="<?=htmlspecialchars($_POST['birth_date'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for = "passport">Паспорт (серия номер) </label>
                    <input type="text" id="passport" name="passport" 
                           placeholder="1234 567890" 
                           value="<?=htmlspecialchars($_POST['passport'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for = "phone">Телефон </label>
                    <input type="tel" id = "phone" name="phone" placeholder="+7 (999) 123-45-67" value="<?=htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for = "address">Адрес проживания </label>
                    <textarea id="address" name="address" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="department_id">Отдел </label>
                    <select id="department_id" name="department_id" required>
                        <option value="">Выберите отдел</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>" 
                                <?= ($_POST['department_id'] ?? '') == $dept['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dept['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="position_id">Должность </label>
                    <select id="position_id" name="position_id" required>
                        <option value="">Выберите должность</option>
                        <?php foreach ($positions as $pos): ?>
                            <option value="<?= $pos['id'] ?>" 
                                <?= ($_POST['position_id'] ?? '') == $pos['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pos['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="salary">Зарплата (₽) </label>
                    <input type="number" id="salary" name="salary" step="0.01" min="0" value="<?= htmlspecialchars($_POST['salary'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="hire_date">Дата приема </label>
                    <input type="date" id="hire_date" name="hire_date" 
                           value="<?= htmlspecialchars($_POST['hire_date'] ?? '') ?>" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Сохранить</button>
                    <a href="index.php" class="btn-cancel">Отмена</a>
                </div>

            </form>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            IMask(document.getElementById('phone'), {
                mask: '+{7} (000) 000-00-00'
            });
    
            IMask(document.getElementById('passport'), {
                mask: '0000 000000'
            });
        });
    </script>
</body>
</html>

