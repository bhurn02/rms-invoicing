<?php
session_start();

// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

// Return progress data
echo json_encode([
    'processed' => $_SESSION['processed'] ?? 0,
    'total' => $_SESSION['total'] ?? 0,
    'message' => end($_SESSION['log']) ?? 'No activity yet.'
]);
