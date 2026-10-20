<?php
declare(strict_types=1);

session_start();

const DATA_DIR = __DIR__ . '/../var/data';

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/storage.php';
require_once __DIR__ . '/catalog.php';