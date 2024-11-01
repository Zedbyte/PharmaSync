<?php

// BaseController.php

namespace App\Controllers;

use Lib\SessionManager;

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
        }
    }

    protected function isAuthenticated(): bool
    {   
        return isset($_SESSION['user_id']) && $_SESSION['logged_in'] === true;
    }
}
