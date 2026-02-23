<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Учет сотрудников</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Список сотрудников</h1>
        <div class="actions">
            <a href="add.php" class="btn btn-add">+ Добавить сотрудника</a>
        </div>
        <table class="e-table">
            <caption>Сотрудники</caption>
            <thead>
                <tr>
                    <th>ФИО</th>
                    <th>Дата рождения</th>
                    <th>Паспорт</th>
                    <th>Телефон</th>
                    <th>Отдел</th>
                    <th>Должность</th>
                    <th>Зарплата</th>
                    <th>Дата приема</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once 'config/database.php';

                $query = "
                    SELECT
                    e.*,
                    d.name AS department_name,
                    p.name AS position_name,
                    CONCAT(e.last_name, ' ', e.first_name, ' ', COALESCE(e.patronymic, '')) AS full_name
                    FROM employees e
                    LEFT JOIN departments d ON e.department_id = d.id
                    LEFT JOIN positions p ON e.position_id = p.id
                    ORDER BY e.last_name, e.first_name
                    ";

                $stmt = $pdo->query($query);
                $employees = $stmt->fetchALL();

                foreach ($employees as $employee):
                    $rowClass = $employee['fired'] ? 'fired' : '';
                ?>

                <tr class="<?= $rowClass ?>" ?>
                    <td><?=  htmlspecialchars($employee['full_name']) ?></td>
                    <td><?=  htmlspecialchars($employee['birth_date']) ?></td>
                    <td><?=  htmlspecialchars($employee['passport']) ?></td>
                    <td><?=  htmlspecialchars($employee['phone']) ?></td>
                    <td><?=  htmlspecialchars($employee['department_name']) ?></td>
                    <td><?=  htmlspecialchars($employee['position_name']) ?></td>
                    <td><?=  htmlspecialchars($employee['salary'], 0, '', ' ') ?></td>
                    <td><?=  htmlspecialchars($employee['hire_date']) ?></td>
                    <td>
                        <?php if ($employee['fired']): ?>
                            <span class="badge fired">Уволен</span>
                        <?php else: ?>
                            <span class= "badge not fired"></span>
                        <?php endif; ?>
                    </td>
                    <td class = "actions">
                        <?php if (!$employee['fired']): ?>
                            <a href="edit.php?id=<?=  $employee['id'] ?>" class ="btn-edit">ред</a>
                            <a href = "delite.php?id<?= $employee['id'] ?>" class = "btn-del" onclick="return confirm('Уволить сотрудника?')">дел</a>
                        <?php else: ?>
                            <span class = "disabled">блок</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>