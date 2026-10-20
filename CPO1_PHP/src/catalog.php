<?php
declare(strict_types=1);

const POSITIONS = ['Профессор', 'Доцент', 'Сеньор-лектор', 'Старший преподаватель', 'Преподаватель'];
const DAYS = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];

function readFilters(array $get): array
{
    $q = isset($get['q']) && is_string($get['q']) ? trim($get['q']) : '';

    $dept = null;
    if (isset($get['dept']) && is_string($get['dept'])) {
        $parsed = filter_var($get['dept'], FILTER_VALIDATE_INT);
        $dept = $parsed === false ? null : $parsed;
    }

    $position = '';
    if (isset($get['position']) && is_string($get['position'])
        && in_array($get['position'], POSITIONS, true)) {
        $position = $get['position'];
    }

    return ['q' => $q, 'dept' => $dept, 'position' => $position];
}

function matchesQuery(array $teacher, string $query): bool
{
    $q = mb_strtolower($query);
    if ($q === '') {
        return true;
    }
    if (str_contains(mb_strtolower($teacher['lastName']), $q)) {
        return true;
    }
    foreach ($teacher['disciplines'] as $discipline) {
        if (str_contains(mb_strtolower($discipline), $q)) {
            return true;
        }
    }
    return false;
}

function applyFilters(array $teachers, array $filters): array
{
    $result = array_filter($teachers, function (array $t) use ($filters): bool {
        if (!matchesQuery($t, $filters['q'])) {
            return false;
        }
        if ($filters['dept'] !== null && $t['departmentId'] !== $filters['dept']) {
            return false;
        }
        if ($filters['position'] !== '' && $t['position'] !== $filters['position']) {
            return false;
        }
        return true;
    });
    return array_values($result);
}

function sortByLastName(array $teachers): array
{
    usort($teachers, fn(array $a, array $b): int =>
        mb_strtolower($a['lastName']) <=> mb_strtolower($b['lastName']));
    return $teachers;
}

function departmentNames(array $departments): array
{
    $names = [];
    foreach ($departments as $d) {
        $names[$d['id']] = $d['name'];
    }
    return $names;
}

function findTeacher(array $teachers, int $id): ?array
{
    foreach ($teachers as $t) {
        if ($t['id'] === $id) {
            return $t;
        }
    }
    return null;
}

function groupScheduleByDay(array $schedule): array
{
    usort($schedule, fn(array $a, array $b): int => strcmp($a['start'], $b['start']));

    $grouped = [];
    foreach (DAYS as $day) {
        foreach ($schedule as $lesson) {
            if ($lesson['day'] === $day) {
                $grouped[$day][] = $lesson;
            }
        }
    }
    return $grouped;
}