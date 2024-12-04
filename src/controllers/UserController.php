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

    // Render View with Error Handling
    private function renderView(string $template, array $data = []): void
    {
        try {
            echo $this->twig->render($template, array_merge(['ASSETS_URL' => ASSETS_URL], $data));
        } catch (Exception $e) {
            echo "Error rendering view: " . $e->getMessage();
        }
    }

    // Display User List Page
    public function display(array $errors = []): void
    {
        try {
            // Fetch data
            $userData = $this->userModel->getAllUsers();
            $totalUsers = $this->userModel->getTotalUsers();
            $totalNewUsers = $this->userModel->getTotalUsers("created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
            $roleStatistics = $this->userModel->getRoleStatistics(); // Fetch role stats
    
            // Render view with data
            $this->renderView('user-list.html.twig', [
                'users' => $userData,
                'total_users' => $totalUsers,
                'total_new_users' => $totalNewUsers,
                'role_statistics' => $roleStatistics, // Pass role stats to the template
                'errors' => $errors,
            ]);
        } catch (Exception $e) {
            $this->renderView('user-list.html.twig', [
                'users' => [],
                'total_users' => 0,
                'total_new_users' => 0,
                'role_statistics' => [],
                'errors' => ["Error: " . $e->getMessage()],
            ]);
        }
    }
    
    

    // Add User Logic
    public function addUser(array $data): void
    {
        try {
            $errors = $this->userModel->checkUniqueConstraints($data);
    
            if (!empty($errors)) {
                $this->display($errors);
                return;
            }
    
            // Handle optional profile picture
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $data['profile_picture'] = $_FILES['profile_picture'];
            } else {
                $data['profile_picture'] = null; // No file uploaded
            }
    
            $this->userModel->save($data);
    
            $this->renderView('user-list.html.twig', [
                'users' => $this->userModel->getAllUsers(),
                'success' => 'User added successfully!',
            ]);
        } catch (Exception $e) {
            $this->display(['error' => 'Failed to add user: ' . $e->getMessage()]);
        }
    }
    

    // Fetch Users with Profile Picture Handling
    public function getUsers(): array
    {
        try {
            $users = $this->userModel->getAllUsers();

            foreach ($users as &$user) {
                $user['profile_picture'] = $user['profile_picture']
                    ? 'data:image/jpeg;base64,' . base64_encode($user['profile_picture'])
                    : $this->getDefaultAvatarBase64();
            }

            return $users;
        } catch (Exception $e) {
            return [];
        }
    }

    private function getDefaultAvatarBase64(): string
    {
        $defaultAvatarPath = __DIR__ . '/../../assets/images/default-avatar.png';
        if (file_exists($defaultAvatarPath)) {
            return 'data:image/jpeg;base64,' . base64_encode(file_get_contents($defaultAvatarPath));
        }
        return '';
    }

    public function deleteUser(array $data): void
    {
        try {
            if (!isset($data['user_id'])) {
                throw new Exception("User ID is required for deletion.");
            }
    
            $this->userModel->delete($data['user_id']);
    
            $this->renderView('user-list.html.twig', [
                'users' => $this->userModel->getAllUsers(),
                'success' => 'User deleted successfully!',
            ]);
        } catch (Exception $e) {
            $this->renderView('user-list.html.twig', [
                'users' => $this->userModel->getAllUsers(),
                'error' => 'Failed to delete user: ' . $e->getMessage(),
            ]);
        }
    }
    
    public function updateUser(array $data): void
    {
        try {
            if (!isset($data['id'])) {
                throw new Exception("User ID is required for update.");
            }
    
            // Fetch existing user
            $existingUser = $this->userModel->findById($data['id']);
            if (!$existingUser) {
                throw new Exception("User not found.");
            }
    
            // Populate missing fields with existing user data
            $data = array_merge($existingUser, $data);
    
            // Check for unique constraints only when critical fields change
            if ($data['email_address'] !== $existingUser['email_address'] ||
                $data['contact_no'] !== $existingUser['contact_no'] ||
                $data['username'] !== $existingUser['username']) {
                $errors = $this->userModel->checkUniqueConstraints($data);
                if (!empty($errors)) {
                    $this->renderView('user-list.html.twig', [
                        'users' => $this->userModel->getAllUsers(),
                        'errors' => $errors,
                    ]);
                    return;
                }
            }
    
            // Handle Profile Picture Upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $data['profile_picture'] = $_FILES['profile_picture'];
            }
    
            // Update the user
            $this->userModel->update($data);
    
            // Redirect to the user list with success message
            $this->renderView('user-list.html.twig', [
                'users' => $this->userModel->getAllUsers(),
                'success' => 'User updated successfully!',
            ]);
        } catch (Exception $e) {
            // Render with error message
            $this->renderView('user-list.html.twig', [
                'users' => $this->userModel->getAllUsers(),
                'error' => 'Failed to update user: ' . $e->getMessage(),
            ]);
        }
    }
    
 
}