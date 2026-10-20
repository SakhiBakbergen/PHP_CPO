<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$error = null;
$teachers = [];
$departments = [];

try {
    $teachers = loadJson(DATA_DIR . '/teachers.json');
    $departments = loadJson(DATA_DIR . '/departments.json');
} catch (RuntimeException $ex) {
    $error = $ex->getMessage();
}

$filters = readFilters($_GET);
$result = sortByLastName(applyFilters($teachers, $filters));
$deptNames = departmentNames($departments);

$pageTitle = 'Каталог преподавателей';
require __DIR__ . '/../templates/header.php';
?>
<h1>Преподаватели</h1>

<?php if ($error !== null): ?>
    <p class="error"><?= e($error) ?></p>
<?php endif; ?>

<form method="get" class="filters">
    <input type="search" name="q" placeholder="Фамилия или дисциплина"
           value="<?= e($filters['q']) ?>">

    <select name="dept">
        <option value="">Все кафедры</option>
        <?php foreach ($departments as $d): ?>
            <option value="<?= (int)$d['id'] ?>"
                <?= $filters['dept'] === $d['id'] ? 'selected' : '' ?>>
                <?= e($d['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="position">
        <option value="">Все должности</option>
        <?php foreach (POSITIONS as $p): ?>
            <option value="<?= e($p) ?>" <?= $filters['position'] === $p ? 'selected' : '' ?>>
                <?= e($p) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Найти</button>
    <a href="/index.php">Сбросить</a>
</form>

<?php if ($error === null && $result === []): ?>
    <p>Ничего не найдено.</p>
<?php endif; ?>

<div class="cards">
    <?php foreach ($result as $t): ?>
        <article class="card">
            <h2>
                <a href="/teacher.php?id=<?= (int)$t['id'] ?>">
                    <?= e($t['lastName'] . ' ' . $t['firstName']) ?>
                </a>
            </h2>
            <p class="position"><?= e($t['position']) ?></p>
            <p><?= e($deptNames[$t['departmentId']] ?? 'Кафедра не указана') ?></p>
            <p class="disciplines"><?= e(implode(', ', $t['disciplines'])) ?></p>
        </article>
    <?php endforeach; ?>
</div>
<?php require __DIR__ . '/../templates/footer.php'; ?>