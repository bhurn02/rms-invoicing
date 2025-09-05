<?php
// config.php - shared in repo, DO NOT edit this for env-specific settings

if (file_exists(__DIR__ . '/config.local.php')) {
    include __DIR__ . '/config.local.php';
} elseif (file_exists(__DIR__ . '/config.live.php')) {
    include __DIR__ . '/config.live.php';
} else {
    die('No valid config found');
}
