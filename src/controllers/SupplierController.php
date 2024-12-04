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

    // Display Suppliers Page
    public function display(array $errors = []): void {
        $status = $_GET['tab'] ?? 'all';

        try {
            $supplierObject = new Supplier();
            $supplierData = $supplierObject->getAllSuppliers();

            echo $this->twig->render('supplier-list.html.twig', [
                'ASSETS_URL' => ASSETS_URL,
                'suppliers' => $supplierData,
                'errors' => $errors,
                'status' => $status
            ]);
        } catch (Exception $e) {
            $errors[] = "Error fetching supplier data: " . $e->getMessage();
            echo $this->twig->render('supplier-list.html.twig', [
                'ASSETS_URL' => ASSETS_URL,
                'suppliers' => [],
                'errors' => $errors,
            ]);
        }
    }

    // Add Supplier Logic
    public function addSupplier(string $name, string $email, string $address, string $contact_no): void {
        try {
            $this->validateSupplierData($name, $email, $address, $contact_no);

            $supplierObject = new Supplier();
            $supplierObject->addSupplier($name, $email, $address, $contact_no);

            $this->redirectWithStatus('/supplier-list', 'success');
        } catch (Exception $e) {
            $this->display(['Error adding supplier: ' . $e->getMessage()]);
        }
    }

    // Update Supplier Logic
    public function updateSupplier(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            $address = $_POST['address'];
            $contact_no = $_POST['contact_no'];

            try {
                $this->validateSupplierData($name, $email, $address, $contact_no);

                $supplierObject = new Supplier();
                if ($supplierObject->updateSupplier($id, $name, $email, $address, $contact_no)) {
                    $this->redirectWithStatus('/supplier-list', 'updated');
                } else {
                    throw new Exception('Failed to update supplier.');
                }
            } catch (Exception $e) {
                $this->display(['Error updating supplier: ' . $e->getMessage()]);
            }
        }
    }

    // Delete Supplier Logic
    public function deleteSupplier(int $supplierId): void {
        try {
            $supplierObject = new Supplier();
            if ($supplierObject->deleteSupplier($supplierId)) {
                $this->redirectWithStatus('/supplier-list', 'deleted');
            } else {
                throw new Exception('Failed to delete supplier.');
            }
        } catch (Exception $e) {
            $this->display(['Error deleting supplier: ' . $e->getMessage()]);
        }
    }

    // Validation for Supplier Data
    private function validateSupplierData(string $name, string $email, string $address, string $contact_no): void {
        $errors = [];

        if (empty($name)) $errors[] = "Name is required.";
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
        if (empty($address)) $errors[] = "Address is required.";
        if (empty($contact_no) || !preg_match('/^\+?[0-9]{7,15}$/', $contact_no)) {
            $errors[] = "Contact number must be between 7-15 digits and can optionally start with '+'.";
        }

        if ($errors) {
            throw new Exception(implode(' ', $errors));
        }
    }

    // Helper Method: Redirect with Status
    private function redirectWithStatus(string $url, string $status): void {
        header('Location: ' . $url . '?status=' . $status);
        exit;
    }
}
