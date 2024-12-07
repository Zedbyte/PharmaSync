<?php
namespace App\Controllers;

use App\Models\Customer;
use Exception;

require_once __DIR__ . '/../../config/config.php';
require_once LIB_URL . '/get_dbcon.inc.php';
require_once 'BaseController.php';

class CustomerController extends BaseController {
    protected $twig;
    protected $db;

    public function __construct($twig) {
        parent::__construct();
        $this->twig = $twig;
        $this->db = returnDBCon(new Customer());
    }

    // Display Customers Page
    public function display(array $errors = []): void
    {
        $status = $_GET['tab'] ?? 'all';

        try {
            $customerModel = new Customer();

            // Fetch customers and total count
            $customerData = $customerModel->getAllCustomers();
            $totalCustomers = $customerModel->getTotalCustomers();

            echo $this->twig->render('customer-list.html.twig', [
                'ASSETS_URL' => ASSETS_URL,
                'customers' => $customerData,
                'total_customers' => $totalCustomers, // Pass total customers
                'errors' => $errors,
                'status' => $status,
            ]);
        } catch (Exception $e) {
            $errors[] = "Error fetching customer data: " . $e->getMessage();
            echo $this->twig->render('customer-list.html.twig', [
                'ASSETS_URL' => ASSETS_URL,
                'customers' => [],
                'total_customers' => 0, // Pass 0 if there's an error
                'errors' => $errors,
            ]);
        }
    }

        // Add Customer
    public function addCustomer(array $data): void
    {
        $errors = [];

        // Validate the customer data
        if (empty($data['name'])) {
            $errors[] = 'Name is required';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }
        if (empty($data['address'])) {
            $errors[] = 'Address is required';
        }
        if (empty($data['contact_no'])) {
            $errors[] = 'Contact number is required';
        }

        if (count($errors) > 0) {
            // Return errors to the view
            echo $this->twig->render('customer-list.html.twig', [
                'ASSETS_URL' => ASSETS_URL,
                'errors' => $errors,
            ]);
        } else {
            // Proceed to add customer to the database
            try {
                $customerModel = new Customer();
                $customerModel->addCustomer($data['name'], $data['email'], $data['address'], $data['contact_no']);

                // Redirect or show success message
                header('Location: /customer-list');
                exit;
            } catch (Exception $e) {
                $errors[] = 'Error adding customer: ' . $e->getMessage();
                echo $this->twig->render('customer-list.html.twig', [
                    'ASSETS_URL' => ASSETS_URL,
                    'errors' => $errors,
                ]);
            }
        }
    }

    // Delete Customer
    public function deleteCustomer(int $customerId): void
    {
        try {
            $customerModel = new Customer();
            if ($customerModel->deleteCustomer($customerId)) {
                // Redirect to customer list with success message
                header('Location: /customer-list?status=deleted');
                exit;
            } else {
                throw new Exception('Failed to delete customer.');
            }
        } catch (Exception $e) {
            $errors[] = 'Error deleting customer: ' . $e->getMessage();
            echo $this->twig->render('customer-list.html.twig', [
                'ASSETS_URL' => ASSETS_URL,
                'errors' => $errors,
            ]);
        }
    }

        // Update Customer
        public function updateCustomer(array $data): void
        {
            $errors = [];
        
            // Validation
            if (empty($data['id'])) {
                $errors[] = 'Customer ID is required.';
            }
            if (empty($data['name'])) {
                $errors[] = 'Name is required.';
            }
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email is required.';
            }
            if (empty($data['address'])) {
                $errors[] = 'Address is required.';
            }
            if (empty($data['contact_no'])) {
                $errors[] = 'Contact number is required.';
            }
        
            if (!empty($errors)) {
                echo $this->twig->render('customer-list.html.twig', [
                    'ASSETS_URL' => ASSETS_URL,
                    'errors' => $errors,
                ]);
                return;
            }
        
            // Perform update
            try {
                $customerModel = new Customer();
                if ($customerModel->updateCustomer(
                    (int) $data['id'],
                    $data['name'],
                    $data['email'],
                    $data['address'],
                    $data['contact_no']
                )) {
                    header('Location: /customer-list?status=updated');
                    exit;
                } else {
                    throw new Exception('Failed to update customer.');
                }
            } catch (Exception $e) {
                $errors[] = 'Error updating customer: ' . $e->getMessage();
                echo $this->twig->render('customer-list.html.twig', [
                    'ASSETS_URL' => ASSETS_URL,
                    'errors' => $errors,
                ]);
            }
        }
        
}