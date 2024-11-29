<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Medicine;
use App\Models\Formulation;
use App\Models\Material;

require_once __DIR__ . '/../../config/config.php';
require_once 'BaseController.php';

class MedicineController extends BaseController 
{

    protected $twig;
    protected $db;

    // Constructor to initialize Twig and BaseController
    public function __construct($twig) {
        parent::__construct();  // Initializes session management in BaseController
        $this->twig = $twig;
        // $this->db = returnDBCon(new MedicineBatch()); // Initialize DB connection once
    }

    public function display($errors = [], $medicineSearch = null)
    {   
        $medicineObject = new Medicine();
        $materialObject = new Material();
        $formulationObject = new Formulation();

        $medicineData = $medicineObject->getAllMedicines();
        $materialData = $materialObject->getAllMaterials();

        foreach($medicineData as &$medicine) {
            $medicine['formulations'] =  $formulationObject->getFormulationByMedicine($medicine['id']);
        }

        echo $this->twig->render('medicine-list.html.twig', [
            'ASSETS_URL' => ASSETS_URL,
            'medicineData' => $medicineData,
            'materialData' => $materialData,
            'errors' => $errors
        ]);
    }

    public function addMedicine() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            
            // Validate the data
            $errors = $this->validateMedicineData($data);
            if (!empty($errors)) {
                $_SESSION['medicine_errors'] = $errors;
                // Redirect to the same route with GET (to prevent resubmission)
                header('Location: /add-medicine');
                exit;
            }
            
            // Save Medicine information
            $medicineObject = new Medicine();
            foreach ($data['medicine_type'] as $index => $medicineType) {
                $medicineObject->save([
                    'medicine_type' => $medicineType,
                    'medicine_name' => $data['medicine_name'][$index],
                    'composition' => $data['composition'][$index],
                    'therapeutic_class' => $data['therapeutic_class'][$index],
                    'regulatory_class' => $data['regulatory_class'][$index],
                    'manufacturing_details' => $data['manufacturing_details'][$index],
                    'unit_price' => $data['unit_price'][$index],
                ]);
            }

            header("Location: /medicine-list");
            exit;
        }

        $errors = isset($_SESSION['medicine_errors']) ? $_SESSION['medicine_errors'] : [];
    
        // Render the template with errors, if any
        $this->display($errors);
        
        // Clear the errors from session after they are displayed
        unset($_SESSION['medicine_errors']);
    }

    public function viewMedicine($data) {

        $medicineObject = new Medicine();
        $medicineData = $medicineObject->getMedicine($data['medicineID']);

        echo $this->twig->render('view-medicine.html.twig', [
            'ASSETS_URL' => ASSETS_URL,
            'medicineData' => $medicineData
        ]);
    }

    public function updateMedicine($data) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Validate the data
            $errors = $this->validateUpdateMedicineData($data);

            if (!empty($errors)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }
    
            // Update Medicine data
            $medicineObject = new Medicine();
            (new Medicine())->update($data['medicineID'],
                [
                'name' => $data['medicine_name'],  
                'type' => $data['medicine_type'],
                'composition' => $data['composition'],
                'therapeutic_class' => $data['therapeutic_class'],
                'regulatory_class' => $data['regulatory_class'],
                'manufacturing_details' => $data['manufacturing_details'],
                'unit_price' => $data['unit_price']
                ]
            );
            
    
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            return;
        }

        $medicineObject = new Medicine();
        $medicineData = $medicineObject->getMedicine($data['medicineID']);

        echo $this->twig->render('update-medicine.html.twig', [
            'medicineData' => $medicineData
        ]);
    }

    public function addFormulation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;

            // Validate the data
            $errors = $this->validateFormulationData($data);
            if (!empty($errors)) {
                $_SESSION['formulation_errors'] = $errors;
                // Redirect to the same route with GET (to prevent resubmission)
                header('Location: /add-formulation');
                exit;
            }
            
            // Save Medicine information
            $formulationObject = new Formulation();
            foreach ($data['quantity_required'] as $index => $unit) {
                $formulationObject->save([
                    'unit' => $unit,
                    'quantity_required' => $data['quantity_required'][$index],
                    'description' => $data['description'][$index],
                    'medicine_id' => $data['medicine_name'][$index],
                    'material_id' => $data['material_name'][$index]
                ]);
            }

            header("Location: /medicine-list");
            exit;
        }

        $errors = isset($_SESSION['formulation_errors']) ? $_SESSION['formulation_errors'] : [];
    
        // Render the template with errors, if any
        $this->display($errors);
        
        // Clear the errors from session after they are displayed
        unset($_SESSION['formulation_errors']);
    }

    public function displayGroq() {
        echo $this->twig->render('ask-groq.html.twig', [
            'ASSETS_URL' => ASSETS_URL
        ]);
    }

    public function sendGroqRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['groq_request'])) {
            $user_query = trim($_POST['groq_request']); // Get the user's query from POST
        
            // GROQ API endpoint and your API key
            $groq_api_url = "https://api.groq.com/openai/v1/chat/completions";
            $api_key =  $_ENV['GROQ_API'];
        
            // Prepare the API request payload
            $payload = [
                "model" => "llama3-70b-8192", // Use the model specified in the example
                "messages" => [
                    [
                        "role" => "user",
                        "content" => $user_query,
                    ]
                ],
                "temperature" => 1,
                "max_tokens" => 1024,
                "top_p" => 1,
                "stream" => false,
                "stop" => null,
            ];
        
            // Initialize cURL
            $ch = curl_init($groq_api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key,
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)); // Convert payload to JSON
        
            // Execute the request and capture the response
            $groq_response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        
            // Handle the response
            if ($http_code === 200) {
                // Success: Send the API response back to the frontend
                header('Content-Type: application/json');
                echo $groq_response;
            } else {
                // Error: Return an error message
                header('Content-Type: application/json');
                echo json_encode(['error' => "Rate limit exceeded. Please wait and try again. HTTP Code: $http_code"]);
            }
        
            exit;
        }
    }

    private function validateMedicineData($data) {
        $errors = [];
        
        // Ensure that medicine_type and medicine_name arrays exist and are not completely empty
        if (empty($data['medicine_type']) || empty(array_filter($data['medicine_name']))) {
            $errors[] = "At least one medicine type and medicine name are required.";
        }
    
        // Validate each entry in the arrays
        if (isset($data['medicine_name']) && is_array($data['medicine_name'])) {
            foreach ($data['medicine_name'] as $index => $medicineName) {
                // Medicine name validation
                if (empty($medicineName)) {
                    $errors[] = "Medicine name for item " . ($index + 1) . " is required.";
                }
    
                // Medicine type validation
                if (!isset($data['medicine_type'][$index]) || empty($data['medicine_type'][$index])) {
                    $errors[] = "Medicine type for item " . ($index + 1) . " is required.";
                }
    
                // Composition validation
                if (!isset($data['composition'][$index]) || empty($data['composition'][$index])) {
                    $errors[] = "Composition for item " . ($index + 1) . " is required.";
                }
    
                // Unit price validation
                if (!isset($data['unit_price'][$index]) || $data['unit_price'][$index] === '') {
                    $errors[] = "Unit price for item " . ($index + 1) . " is required.";
                } elseif (!is_numeric($data['unit_price'][$index]) || $data['unit_price'][$index] <= 0) {
                    $errors[] = "Unit price for item " . ($index + 1) . " must be a positive number.";
                }
    
                // Therapeutic class validation
                if (!isset($data['therapeutic_class'][$index]) || empty($data['therapeutic_class'][$index])) {
                    $errors[] = "Therapeutic class for item " . ($index + 1) . " is required.";
                }
    
                // Regulatory class validation
                if (!isset($data['regulatory_class'][$index]) || empty($data['regulatory_class'][$index])) {
                    $errors[] = "Regulatory class for item " . ($index + 1) . " is required.";
                }
    
                // Manufacturing details validation
                if (!isset($data['manufacturing_details'][$index]) || empty($data['manufacturing_details'][$index])) {
                    $errors[] = "Manufacturing details for item " . ($index + 1) . " are required.";
                }
            }
        } else {
            $errors[] = "Medicine data is missing or improperly formatted.";
        }
    
        return $errors;
    }
    
    private function validateUpdateMedicineData($data)
    {
        $errors = [];

        // Medicine ID validation
        if (!isset($data['medicine_id']) || empty($data['medicine_id'])) {
            $errors[] = "Medicine ID is required.";
        } elseif (!is_numeric($data['medicine_id']) || $data['medicine_id'] <= 0) {
            $errors[] = "Medicine ID must be a positive integer.";
        }

        // Medicine type validation
        if (!isset($data['medicine_type']) || empty($data['medicine_type'])) {
            $errors[] = "Medicine type is required.";
        }

        // Medicine name validation
        if (!isset($data['medicine_name']) || empty($data['medicine_name'])) {
            $errors[] = "Medicine name is required.";
        }

        // Composition validation
        if (!isset($data['composition']) || empty($data['composition'])) {
            $errors[] = "Composition is required.";
        }

        // Unit price validation
        if (!isset($data['unit_price']) || $data['unit_price'] === '') {
            $errors[] = "Unit price is required.";
        } elseif (!is_numeric($data['unit_price']) || $data['unit_price'] <= 0) {
            $errors[] = "Unit price must be a positive number.";
        }

        // Therapeutic class validation
        if (!isset($data['therapeutic_class']) || empty($data['therapeutic_class'])) {
            $errors[] = "Therapeutic class is required.";
        }

        // Regulatory class validation
        if (!isset($data['regulatory_class']) || empty($data['regulatory_class'])) {
            $errors[] = "Regulatory class is required.";
        }

        // Manufacturing details validation
        if (!isset($data['manufacturing_details']) || empty($data['manufacturing_details'])) {
            $errors[] = "Manufacturing details are required.";
        }

        return $errors;
    }

    private function validateFormulationData($data) {
        $errors = [];
        
        // Ensure at least one formulation exists
        if (empty($data['medicine_name']) || empty(array_filter($data['medicine_name'])) || 
            empty($data['material_name']) || empty(array_filter($data['material_name']))) {
            $errors[] = "At least one medicine and material name are required.";
        }
        
        // Validate each formulation entry
        $maxEntries = max(
            isset($data['medicine_name']) ? count($data['medicine_name']) : 0,
            isset($data['material_name']) ? count($data['material_name']) : 0,
            isset($data['quantity_required']) ? count($data['quantity_required']) : 0
        );
    
        for ($index = 0; $index < $maxEntries; $index++) {
            // Medicine name validation
            if (!isset($data['medicine_name'][$index]) || empty($data['medicine_name'][$index])) {
                $errors[] = "Medicine name for entry " . ($index + 1) . " is required.";
            }
    
            // Material name validation
            if (!isset($data['material_name'][$index]) || empty($data['material_name'][$index])) {
                $errors[] = "Material name for entry " . ($index + 1) . " is required.";
            }
    
            // Quantity required validation
            if (!isset($data['quantity_required'][$index]) || $data['quantity_required'][$index] === '') {
                $errors[] = "Quantity required for entry " . ($index + 1) . " is required.";
            } elseif (!is_numeric($data['quantity_required'][$index]) || $data['quantity_required'][$index] <= 0) {
                $errors[] = "Quantity required for entry " . ($index + 1) . " must be a positive number.";
            }
    
            // Unit validation
            if (!isset($data['unit'][$index]) || empty($data['unit'][$index])) {
                $errors[] = "Unit for entry " . ($index + 1) . " is required.";
            }
    
            // Description validation (optional but checked for completeness)
            if (!isset($data['description'][$index]) || empty($data['description'][$index])) {
                $errors[] = "Description for entry " . ($index + 1) . " is required.";
            }
        }
    
        return $errors;
    }

}