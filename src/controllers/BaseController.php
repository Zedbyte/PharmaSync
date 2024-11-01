<?php

// BaseController.php

namespace App\Controllers;

use Lib\SessionManager;
use App\Models\User;

class BaseController
{
    protected $sessionManager;

    public function __construct()
    {
        $this->initializeSession();
    }

    protected function initializeSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            // Create and initialize SessionManager instance with a 30-minute interval
            $this->sessionManager = new SessionManager(1800);
            $this->sessionManager->manageSession();

            // Check for "Remember Me" cookies and restore session if valid
            $this->checkRememberMeCookies();
        }
    }

    protected function isAuthenticated(): bool
    {   
        return isset($_SESSION['user_id']) && $_SESSION['logged_in'] === true;
    }

    private function checkRememberMeCookies()
    {
        if (isset($_COOKIE['user_id']) && isset($_COOKIE['email_address'])) {
            $userID = $_COOKIE['user_id'];
            $userEmail = $_COOKIE['email_address'];

            // Fetch user based on the ID and email to validate the cookie
            $userObject = new User();
            $user = $userObject->findById($userID); // Implement findById method in your User model

            // Check if user exists and email matches
            if ($user && $user['email_address'] === $userEmail) {
                // Restore session data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email_address'] = $user['email_address'];
                $_SESSION['logged_in'] = true;

                // Optional: Update last activity time to refresh session
                $_SESSION['last_regeneration'] = time();
            }
        }
    }
}
