<?php

namespace App\Middleware;

class AuthMiddleware
{
    public static function checkAuth()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['logged_in'] !== true) {
            header("Location: /login");
            exit();
        }
    }
}
