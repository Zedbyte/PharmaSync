<?php

namespace App\Controllers;

use App\Models\User;
use \Exception;

require_once __DIR__ . '/../../config/config.php';
require_once 'BaseController.php';

class RegistrationController extends BaseController 
{
    protected $twig;

    // Constructor to initialize Twig and BaseController
    public function __construct($twig) {
        parent::__construct();  // Initializes session management in BaseController
        $this->twig = $twig;
    }

    // Display login page
    public function display($errors=[]) {

        if (isset($_SESSION['registration_error'])) {    
            $errors = $_SESSION['registration_error'];
            $this->renderRegistrationPageError($errors);
            // Clear the errors from session after they are displayed
            unset($_SESSION['registration_error']);
            exit;
        }
        echo $this->twig->render('registration.html.twig', ['ASSETS_URL' => ASSETS_URL]);
    }

    // Helper method to render registration page with error
    private function renderRegistrationPageError($errors) {
        echo $this->twig->render('registration.html.twig', [
            'ASSETS_URL' => ASSETS_URL,
            'errors' => $errors
        ]);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Validate the data
                $errors = $this->validateRegistrationData($_POST);

                // Check for unique constraints
                if (empty($errors)) {
                    $userObject = new User();
                    $uniqueErrors = $userObject->checkUniqueConstraints($_POST);
                    $errors = array_merge($errors, $uniqueErrors);
                }

                // If there are errors, display them
                if (!empty($errors)) {
                    $_SESSION['registration_error'] = $errors;
                    header('Location: /registration');
                    exit;
                }

                // Prepare data for the save method
                $userData = [
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'username' => $_POST['username'],
                    'contact_no' => $_POST['contact_no'],
                    'email_address' => $_POST['email_address'],
                    'password' => $_POST['password'],
                    'role' => $_POST['role'] ?? 'staff',  // default to 'Staff' if not provided
                    'gender' => $_POST['gender']
                ];

                // Save the user
                try {
                    $userObject = new User();
                    $userID = $userObject->save($userData);
                    // Redirect or display success message as needed
                    header("Location: /login");
                    exit;
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                    $this->renderRegistrationPageError($errors);
                }
        }
    } 

    private function validateRegistrationData($data)
    {
        $errors = [];

        if (empty($data['first_name'])) {
            $errors['first_name'] = 'First name is required.';
        }

        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Last name is required.';
        }

        if (empty($data['email_address']) || !filter_var($data['email_address'], FILTER_VALIDATE_EMAIL)) {
            $errors['email_address'] = 'A valid email address is required.';
        }

        if (empty($data['contact_no']) || !preg_match('/^\+?\d{10,15}$/', $data['contact_no'])) {
            $errors['contact_no'] = 'A valid contact number is required.';
        }

        if (empty($data['username'])) {
            $errors['username'] = 'Username is required.';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Password is required.';
        } elseif ($data['password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        if (empty($data['role']) || !in_array($data['role'], ['administrator', 'inventory_manager', 'finance_manager', 'hr_manager', 'staff'])) {
            $errors['role'] = 'Invalid role selected.';
        }

        return $errors;
    }

}