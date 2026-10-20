<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$error = null;
$teacher = null;
$deptName = '';

try {
    $teachers = loadJson(DATA_DIR . '/teachers.json');
    $departments = loadJson(DATA_DIR . '/departments.json');

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id !== null && $id !== false) {
        $teacher = findTeacher($teachers, $id);
    }
    if ($teacher !== null) {
        $deptName = departmentNames($departments)[$teacher['departmentId']] ?? 'Кафедра не указана';
    }
} catch (RuntimeException $ex) {
    $error = $ex->getMessage();
}

if ($error === null && $teacher === null) {
    http_response_code(404);
}

$pageTitle = $teacher !== null ? $teacher['lastName'] . ' ' . $teacher['firstName'] : 'Не найдено';
require __DIR__ . '/../templates/header.php';
?>
<p><a class="back" href="/index.php">&larr; Назад к каталогу</a></p>

<?php if ($error !== null): ?>
    <p class="error"><?= e($error) ?></p>

<?php elseif ($teacher === null): ?>
    <h1>Преподаватель не найден</h1>
    <p>Записи с таким номером нет. Вернитесь в каталог и выберите преподавателя из списка.</p>

<?php else: ?>
    <section class="profile">
        <h1><?= e($teacher['lastName'] . ' ' . $teacher['firstName']) ?></h1>
        <p><span class="position"><?= e($teacher['position']) ?></span></p>
        <p><b>Кафедра:</b> <?= e($deptName) ?></p>
        <p><b>Дисциплины:</b> <?= e(implode(', ', $teacher['disciplines'])) ?></p>
        <p><b>Email:</b> <?= e($teacher['email']) ?></p>
        <p><?= e($teacher['bio']) ?></p>
    </section>

    <h2 class="section-title">Расписание</h2>
    <?php $byDay = groupScheduleByDay($teacher['schedule'] ?? []); ?>

    <?php if ($byDay === []): ?>
        <p>Занятий нет.</p>
    <?php endif; ?>

    <?php foreach ($byDay as $day => $lessons): ?>
        <div class="day">
            <h3><?= e($day) ?></h3>
            <table class="schedule">
                <?php foreach ($lessons as $l): ?>
                    <tr>
                        <td class="time"><?= e($l['start'] . '–' . $l['end']) ?></td>
                        <td><?= e($l['discipline']) ?></td>
                        <td>
                            <span class="badge <?= $l['type'] === 'лекция' ? 'lecture' : 'lab' ?>">
                                <?= e($l['type']) ?>
                            </span>
                        </td>
                        <td class="room"><?= e($l['room']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<?php require __DIR__ . '/../templates/footer.php'; ?>