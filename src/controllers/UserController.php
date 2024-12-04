<?php
namespace App\Controllers;

use App\Models\User;
use Exception;

require_once __DIR__ . '/../../config/config.php';
require_once LIB_URL . '/get_dbcon.inc.php';
require_once 'BaseController.php';

class UserController extends BaseController
{
    protected $twig;
    protected $db;
    protected $userModel;

    public function __construct($twig)
    {
        parent::__construct();
        $this->twig = $twig;
        $this->db = returnDBCon(new User());
        $this->userModel = new User();
    }

    public function displayUsers(array $errors = [])
    {
        try {
            $userModel = new User();
            $users = $userModel->getAllUsersWithBase64Pictures();
            $totalUsers = $userModel->getTotalUsersCount();
            $roleCounts = $userModel->countRoles();
            $recentActivities = $userModel->getRecentActivities();

            echo $this->twig->render('users-list.html.twig', [
                'ASSETS_URL' => ASSETS_URL,
                'users' => $users,
                'total_users' => $totalUsers,
                'role_counts' => $roleCounts,
                'recent_activities' => $recentActivities,
                'errors' => $errors,
            ]);
        } catch (Exception $e) {
            $errors[] = "Error fetching user data: " . $e->getMessage();
            echo $this->twig->render('users-list.html.twig', [
                'ASSETS_URL' => ASSETS_URL,
                'users' => [],
                'recent_activities' => [],
                'role_counts' => [],
                'total_users' => 0,
                'errors' => $errors,
            ]);
        }
    }

    public function addUser($data)
    {
        try {
            // Validate the incoming data
            $errors = [];
            if (empty($data['first_name'])) {
                $errors[] = "First name is required.";
            }
            if (empty($data['last_name'])) {
                $errors[] = "Last name is required.";
            }
            if (empty($data['email_address']) || !filter_var($data['email_address'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "A valid email address is required.";
            }
            if (empty($data['contact_no'])) {
                $errors[] = "Contact number is required.";
            }
            if (empty($data['username'])) {
                $errors[] = "Username is required.";
            }
            if (empty($data['password'])) {
                $errors[] = "Password is required.";
            }
            if (empty($data['role'])) {
                $errors[] = "Role is required.";
            }
            if (empty($data['gender'])) {
                $errors[] = "Gender is required.";
            }
    
            // Check for unique constraints
            $userModel = new User();
            $uniqueErrors = $userModel->checkUniqueConstraints($data);
            $errors = array_merge($errors, $uniqueErrors);
    
            if (!empty($errors)) {
                // Return errors to the form
                echo $this->twig->render('users-list.html.twig', [
                    'errors' => $errors,
                    'users' => $userModel->getAllUsers(),
                ]);
                return;
            }
    
            // Handle file upload for profile picture
            $profilePicture = null;
            if (!empty($_FILES['profile_picture']['tmp_name'])) {
                $profilePicture = file_get_contents($_FILES['profile_picture']['tmp_name']);
            } else {
                // Use default profile picture if none is uploaded
                $profilePicture = file_get_contents(__DIR__ . '/../../public/assets/images/default-profile.png');
            }
            $data['profile_picture'] = $profilePicture;
    
            // Save the user
            $userModel->save($data);
    
            // Log the activity
            $description = "Added a new user: {$data['first_name']} {$data['last_name']}.";
            $userModel->logActivity('Add', $userId, $description);

            // Redirect back to the user list with a success message
            header("Location: /users-list?status=success");
            exit;
        } catch (Exception $e) {
            // Handle exceptions and return errors
            $errors[] = "Error adding user: " . $e->getMessage();
            echo $this->twig->render('users-list.html.twig', [
                'errors' => $errors,
                'users' => [],
            ]);
        }
    }
    

    public function deleteUser($id)
    {
        try {
            // Initialize the User model
            $userModel = new User();

            // Attempt to delete the user
            if ($userModel->deleteById($id)) {
                // Log the activity
                $description = "Deleted user: {$data['first_name']} {$data['last_name']}.";
                $userModel->logActivity('Delete', $userId, $description);

                // Redirect back to the user list with a success message
                header("Location: /users-list?status=deleted");
                exit;
            } else {
                throw new Exception("Failed to delete user.");
            }
        } catch (Exception $e) {
            // Handle errors and return to the list page with an error message
            $errors[] = "Error deleting user: " . $e->getMessage();
            echo $this->twig->render('users-list.html.twig', [
                'errors' => $errors,
                'users' => [],
            ]);
        }
    }

    public function updateUser($data)
    {
        try {
            // Validate input data
            $errors = [];
            if (empty($data['first_name'])) {
                $errors[] = "First name is required.";
            }
            if (empty($data['last_name'])) {
                $errors[] = "Last name is required.";
            }
            if (empty($data['email_address']) || !filter_var($data['email_address'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "A valid email address is required.";
            }
            if (empty($data['contact_no'])) {
                $errors[] = "Contact number is required.";
            }
            if (empty($data['role'])) {
                $errors[] = "Role is required.";
            }
            if (empty($data['gender'])) {
                $errors[] = "Gender is required.";
            }

            // Handle errors
            if (!empty($errors)) {
                $userModel = new User();
                echo $this->twig->render('users-list.html.twig', [
                    'errors' => $errors,
                    'users' => $userModel->getAllUsers(),
                ]);
                return;
            }

            // Prepare profile picture
            $profilePicture = null;
            if (!empty($_FILES['profile_picture']['tmp_name'])) {
                $profilePicture = file_get_contents($_FILES['profile_picture']['tmp_name']);
            }

            // Update user in the database
            $userModel = new User();
            $userModel->update([
                'id' => $data['id'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email_address' => $data['email_address'],
                'contact_no' => $data['contact_no'],
                'role' => $data['role'],
                'gender' => $data['gender'],
                'profile_picture' => $profilePicture,
            ]);
                // Log the activity
                $description = "Updated user: {$data['first_name']} {$data['last_name']}.";
                $userModel->logActivity('Edit', $userId, $description);
            // Redirect to user list
            header("Location: /users-list?status=updated");
            exit;
        } catch (Exception $e) {
            // Handle errors
            $errors[] = "Error updating user: " . $e->getMessage();
            echo $this->twig->render('users-list.html.twig', [
                'errors' => $errors,
                'users' => [],
            ]);
        }
    }  


    public function getTotalUsers()
    {
        try {
            $userModel = new User();
            return $userModel->getTotalUsersCount();
        } catch (Exception $e) {
            error_log("Error fetching total user count: " . $e->getMessage());
            return 0;
        }
    }

    private function validateUserData($data, $isUpdate = false)
    {
        $errors = [];
        if (empty($data['first_name'])) $errors[] = "First name is required.";
        if (empty($data['last_name'])) $errors[] = "Last name is required.";
        if (empty($data['email_address']) || !filter_var($data['email_address'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "A valid email address is required.";
        }
        if (empty($data['contact_no'])) $errors[] = "Contact number is required.";
        if (!$isUpdate && empty($data['password'])) $errors[] = "Password is required.";
        if (empty($data['role'])) $errors[] = "Role is required.";
        if (empty($data['gender'])) $errors[] = "Gender is required.";

        return $errors;
    }

    private function prepareProfilePicture()
    {
        if (!empty($_FILES['profile_picture']['tmp_name'])) {
            return file_get_contents($_FILES['profile_picture']['tmp_name']);
        }

        return file_get_contents(__DIR__ . '/../../public/assets/images/default-profile.png');
    }
}
