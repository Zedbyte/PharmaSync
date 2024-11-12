<?php

namespace App\Controllers;

use App\Models\User;

require_once __DIR__ . '/../../config/config.php';
require_once 'BaseController.php';

class LoginController extends BaseController 
{
    protected $twig;

    // Constructor to initialize Twig and BaseController
    public function __construct($twig) {
        parent::__construct();  // Initializes session management in BaseController
        $this->twig = $twig;
    }

    // Display login page
    public function display() {
        if ($this->isAuthenticated()) {
            header("Location: /dashboard");
            exit();
        }
        if (isset($_SESSION['login_error'])) {    
            $error = $_SESSION['login_error'];
            $this->renderLoginPageError($error);
            // Clear the errors from session after they are displayed
            unset($_SESSION['login_error']);
        }
        echo $this->twig->render('login.html.twig', ['ASSETS_URL' => ASSETS_URL]);
    }

    // Login function
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? null;
            $remember = isset($_POST['remember']);
    
            if ($email && $password) {
                $user = $this->authenticateUser($email, $password);
    
                if ($user) {
                    // Session data is set after verifying user credentials
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email_address'] = $user['email_address'];
                    $_SESSION['logged_in'] = true;
    
                    if ($remember) {
                        $this->setRememberMeCookies($user);
                    }
    
                    header("Location: /dashboard");
                    exit();
                } else {
                    $_SESSION['login_error'] = "Invalid email or password";
                    header('Location: /login');
                    exit;
                }
            } else {
                $_SESSION['login_error'] = "Please enter both email and password";
                header('Location: /login');
                exit;
            }
        } else {
            header("Location: /login");
            exit();
        }
    }
    

    // Logout function
    public function logout() {
        $this->clearSession();
        $this->clearRememberMeCookies();

        header("Location: /login");
        exit();
    }

    // Authentication function
    protected function authenticateUser($email, $password) {
        $userObject = new User();
        return $userObject->verifyAccess($email, $password);
    }

    // Helper method to render login page with error
    private function renderLoginPageError($error) {
        echo $this->twig->render('login.html.twig', [
            'ASSETS_URL' => ASSETS_URL,
            'error' => $error
        ]);
    }

    // Set "Remember Me" cookies for 30 days
    private function setRememberMeCookies($user) {
        setcookie('user_id', $user['id'], time() + (86400 * 30), "/");
        setcookie('email_address', $user['email_address'], time() + (86400 * 30), "/");
    }

    // Clear session and reset session manager
    private function clearSession() {
        session_unset();
        session_destroy();
        $this->sessionManager = null;
    }

    // Clear "Remember Me" cookies
    private function clearRememberMeCookies() {
        if (isset($_COOKIE['user_id'])) {
            setcookie('user_id', '', time() - 3600, "/");
            setcookie('email_address', '', time() - 3600, "/");
        }
    }
}
