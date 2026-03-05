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

         <div class="filters-container">
            <form method="GET" action="index.php" class="filters-form">
                <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <div class="filter-group">
                    <label for="department">Отдел:</label>
                    <select name="department" id="department" class="filter-select">
                        <option value="">Все отделы</option>
                        <?php
                        require_once 'config/database.php';
                        $depts = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll();
                        foreach ($depts as $dept):
                            $selected = ($_GET['department'] ?? '') == $dept['id'] ? 'selected' : '';
                        ?>
                            <option value="<?= $dept['id'] ?>" <?= $selected ?>>
                                <?= htmlspecialchars($dept['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="position">Должность:</label>
                    <select name="position" id="position" class="filter-select">
                        <option value="">Все должности</option>
                        <?php
                        $positions = $pdo->query("SELECT * FROM positions ORDER BY name")->fetchAll();
                        foreach ($positions as $pos):
                            $selected = ($_GET['position'] ?? '') == $pos['id'] ? 'selected' : '';
                        ?>
                            <option value="<?= $pos['id'] ?>" <?= $selected ?>>
                                <?= htmlspecialchars($pos['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-filter">Применить фильтры</button>
                    <a href="index.php" class="btn-filter-clear">Сбросить всё</a>
                </div>
            </form>
        </div>

        <div class="search-container">
            <form method="GET" action="index.php" class="search-form">
                <input type="text" 
                       name="search" 
                       placeholder="Введите ФИО для поиска..." 
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                       class="search-input">
                <?php if (!empty($_GET['department'])): ?>
                    <input type="hidden" name="department" value="<?= htmlspecialchars($_GET['department']) ?>">
                <?php endif; ?>
                <?php if (!empty($_GET['position'])): ?>
                    <input type="hidden" name="position" value="<?= htmlspecialchars($_GET['position']) ?>">
                <?php endif; ?>

                <button type="submit" class="btn-search">Найти</button>
                <?php if (!empty($_GET['search'])): ?>
                    <a href="index.php" class="btn-clear">Очистить</a>
                <?php endif; ?>
            </form>
        </div>

        <table class="e-table">
            <h1>Сотрудники</h1>
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
                $search = $_GET['search'] ?? '';
                $department_id = $_GET['department'] ?? '';
                $position_id = $_GET['position'] ?? '';
                require_once 'config/database.php';

                $search = $_GET['search'] ?? '';

                $query = "
                    SELECT
                    e.*,
                    d.name AS department_name,
                    p.name AS position_name,
                    CONCAT(e.last_name, ' ', e.first_name, ' ', COALESCE(e.patronymic, '')) AS full_name
                    FROM employees e
                    LEFT JOIN departments d ON e.department_id = d.id
                    LEFT JOIN positions p ON e.position_id = p.id
                    WHERE 1=1
                    ";

                $params = [];

                if (!empty($search)) {
                    $query .= " WHERE CONCAT(e.last_name, ' ', e.first_name, ' ', COALESCE(e.patronymic, '')) ILIKE :search";
                }

                if (!empty($department_id)) {
                    $query .= " AND e.department_id = :department_id";
                    $params['department_id'] = $department_id;
                }

                if (!empty($position_id)) {
                    $query .= " AND e.position_id = :position_id";
                    $params['position_id'] = $position_id;
                }

                $query .= " ORDER BY e.last_name, e.first_name";

                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                $employees = $stmt->fetchAll();
                
                $stmt->execute();
                $employees = $stmt->fetchALL();

                if (count($employees) > 0):
                    foreach ($employees as $employee):
                        $rowClass = $employee['fired'] ? 'fired' : '';
                ?>

                <tr class="<?= $rowClass ?>" >
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
                            <span class= "badge not-fired">Работает</span>
                        <?php endif; ?>
                    </td>
                    <td class = "actions-s">
                        <?php if (!$employee['fired']): ?>
                            <a href="edit.php?id=<?=  $employee['id'] ?>" class ="btn-s edit">ред</a>
                            <a href="delete.php?id=<?= $employee['id'] ?>" class="btn-s del" onclick="return confirm('Уволить сотрудника?')">уволить</a>
                        <?php else: ?>
                            <span class = "disabled">блок</span>
                            <a href="restore.php?id=<?= $employee['id'] ?>" class="btn-s rest" onclick="return confirm('Восстановить сотрудника?')">восстановить</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; 
                else:
                ?>
                <tr>
                    <td colspan="10" class="no-results">По вашему запросу ничего не найдено</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="record-count">
            Найдено записей: <?= count($employees) ?>
        </div>
    </div>
</body>
</html>