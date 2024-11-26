<?php

namespace App\Middleware;

use App\Models\User;

class AuthMiddleware
{
    public static function checkAuth()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['logged_in'] !== true) {
            header("Location: /login");
            exit();
        }
    }

    public static function checkRole(array $allowedRoles = [])
    {
        $userObject = new User();
        $userRole = $userObject->getRoleById($_SESSION['user_id']); // Fetch the user's role
    
        // Check if the role is allowed
        if ($userRole !== 'administrator' && !in_array($userRole, $allowedRoles)) {
            // Deny access: Redirect to an error page or return a 403 HTTP response
            http_response_code(403);
            echo "Access Denied: You do not have permission to access this page.";
            exit;
        }
    
        // If role is allowed, the middleware passes
    }
}
