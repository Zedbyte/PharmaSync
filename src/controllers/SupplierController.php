<?php
namespace App\Controllers;

use App\Models\Supplier;
use Exception;

require_once __DIR__ . '/../../config/config.php';
require_once LIB_URL . '/get_dbcon.inc.php';
require_once 'BaseController.php';

class SupplierController extends BaseController {
    protected $twig;
    protected $db;

    public function __construct($twig) {
        parent::__construct();
        $this->twig = $twig;
        $this->db = returnDBCon(new Supplier());
    }

    // Display supplier Page
    public function display(array $errors = []): void
    {
        $status = $_GET['tab'] ?? 'all';

        try {
            $supplierModel = new Supplier();

            // Fetch suppliers and total count
            $supplierData = $supplierModel->getAllSuppliers();
            $totalSuppliers = $supplierModel->getTotalSuppliers();

            echo $this->twig->render('supplier-list.html.twig', [
                'ASSETS_URL' => ASSETS_URL,
                'suppliers' => $supplierData,
                'total_suppliers' => $totalSuppliers,
                'errors' => $errors,
                'status' => $status,
            ]);
        } catch (Exception $e) {
            $errors[] = "Error fetching supplier data: " . $e->getMessage();
            echo $this->twig->render('supplier-list.html.twig', [
                'ASSETS_URL' => ASSETS_URL,
                'suppliers' => [],
                'total_suppliers' => 0,
                'errors' => $errors,
            ]);
        }
    }

    // Add supplier
    public function addSupplier(array $data): void
    {
        $errors = [];

        // Validate the supplier data
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
            $this->display($errors);
        } else {
            // Proceed to add supplier to the database
            try {
                $supplierModel = new Supplier();
                $supplierModel->addSupplier($data['name'], $data['email'], $data['address'], $data['contact_no']);

                // Redirect or show success message
                header('Location: /supplier-list');
                exit;
            } catch (Exception $e) {
                $errors[] = 'Error adding supplier: ' . $e->getMessage();
                $this->display($errors);
            }
        }
    }

    // Delete supplier
    public function deleteSupplier(int $supplierId): void
    {
        try {
            $supplierModel = new Supplier();
            if ($supplierModel->deleteSupplier($supplierId)) {
                // Redirect to supplier list with success message
                header('Location: /supplier-list?status=deleted');
                exit;
            } else {
                throw new Exception('Failed to delete supplier.');
            }
        } catch (Exception $e) {
            $errors[] = 'Error deleting supplier: ' . $e->getMessage();
            $this->display($errors);
        }
    }

    // Update supplier
    public function updateSupplier(array $data): void
    {
        $errors = [];

        // Validation
        if (empty($data['id'])) {
            $errors[] = 'Supplier ID is required.';
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
            $this->display($errors);
            return;
        }

        // Perform update
        try {
            $supplierModel = new Supplier();
            if ($supplierModel->updateSupplier(
                (int) $data['id'],
                $data['name'],
                $data['email'],
                $data['address'],
                $data['contact_no']
            )) {
                header('Location: /supplier-list?status=updated');
                exit;
            } else {
                throw new Exception('Failed to update supplier.');
            }
        } catch (Exception $e) {
            $errors[] = 'Error updating supplier: ' . $e->getMessage();
            $this->display($errors);
        }
    }
}
