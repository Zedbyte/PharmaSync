<?php

function redirect_json($success, $errors = []) {
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'errors' => $errors]);
    exit;
}