<?php

namespace Lib;

class SessionManager
{
    private $interval;
    
    public function __construct($interval = 1800)
    {
        $this->interval = $interval;
        ini_set('session.use_only_cookies', 1);
        ini_set('session.use_strict_mode', 1);
        
        session_set_cookie_params([
            'lifetime' => $interval,
            'path' => '/',
            'domain' => 'localhost',
            'secure' => true,
            'httponly' => true,
        ]);

        session_start();
    }

    public function manageSession()
    {
        if (isset($_SESSION["user_id"])) {
            $this->regenerateSession('loggedIn');
        } else {
            $this->regenerateSession();
        }
    }

    private function regenerateSession($type = 'guest')
    {
        if (!isset($_SESSION['last_regeneration']) || time() - $_SESSION['last_regeneration'] >= $this->interval) {
            if ($type === 'loggedIn') {
                $this->regenerateSessionIdLoggedIn();
            } else {
                $this->regenerateSessionId();
            }
        }
    }

    private function regenerateSessionId()
    {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }

    private function regenerateSessionIdLoggedIn()
    {
        session_regenerate_id(true);

        $userID = $_SESSION["user_id"];
        $newSessionID = session_create_id() . "_$userID";
        session_id($newSessionID);

        $_SESSION['last_regeneration'] = time();
    }
}