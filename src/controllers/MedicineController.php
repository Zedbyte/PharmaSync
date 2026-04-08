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
                    'unit' => $data['unit'][$index],
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

    public function deleteMedicine($medicineID) {
        // $medicineObject = new Medicine();
        // $medicineObject->delete($medicineID);
        // header("Location: /medicine-list");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // $purchaseMaterialObject = new PurchaseMaterial();

            // $errors = $purchaseMaterialObject->deletePurchaseData($purchaseID, $_POST['deleteMaterials']);
            
            $medicineObject = new Medicine();
            $errors = $medicineObject->delete($medicineID, $_POST['deleteMedicine']);

            $tempErrors = [];
            if (!empty($errors)) {
                foreach ($errors as $errorArray) {
                    $tempErrors = array_merge($tempErrors, $errorArray);
                }
                // Assign the flattened errors back to the original $errors variable
                $errors = $tempErrors;
                $_SESSION['delete_med_errors'] = $errors;
                echo json_encode(['success' => true, 'redirect' => '/delete-medicine']);
                exit;
            }

            echo json_encode(['success' => true, 'redirect' => '/medicine-list']);
            exit;
        }

        $errors = isset($_SESSION['delete_med_errors']) ? $_SESSION['delete_med_errors'] : [];

        // Render the template with errors, if any
        $this->display($errors);
        // Clear the errors from session after they are displayed
        unset($_SESSION['delete_med_errors']);
    }

    public function updateFormulation($data) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Validate the data
            $errors = $this->validateUpdateFormulationData($data);

            if (!empty($errors)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }
    
            // Update Medicine data
            (new Formulation())->update($data['formulation_id'],
                [
                'medicine_id' => $data['medicine_name'],  
                'material_id' => $data['material_name'],
                'quantity_required' => $data['quantity_required'],
                'unit' => $data['unit'],
                'description' => $data['description'],
                ]
            );
            
    
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            return;
        }

        $medicineData = (new Medicine())->getAllMedicines();
        $materialData = (new Material())->getAllMaterials();

        $formulationObject = new Formulation();
        $formulationData = $formulationObject->getFormulationByID($data['formulationID']);

        echo $this->twig->render('update-formulation.html.twig', [
            'formulationData' => $formulationData,
            'medicineData' => $medicineData,
            'materialData' => $materialData
        ]);
    }

    public function deleteFormulation($formulationID) {
        $formulationObject = new Formulation();
        $formulationObject->delete($formulationID);
        header("Location: /medicine-list");
    }

    public function viewFormulation($data) {
        $formulationObject = new Formulation();
        $formulationData = $formulationObject->getFormulationByID($data['formulationID']);
        // var_dump($formulationData);
        echo $this->twig->render('view-formulation.html.twig', [
            'ASSETS_URL' => ASSETS_URL,
            'formulationData' => $formulationData
        ]);
    }

    public function displayGroq() {
        echo $this->twig->render('ask-groq.html.twig', [
            'ASSETS_URL' => ASSETS_URL
        ]);
    }

    public function sendGroqRequest() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed.']);
            return;
        }

        $user_query = trim($_POST['groq_request'] ?? '');
        if ($user_query === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Please enter a prompt before sending.']);
            return;
        }

        $config = $this->getGroqConfig();
        if ($config['api_key'] === '') {
            http_response_code(500);
            echo json_encode([
                'error' => 'Chatbot is not configured. Set GROQ_API_KEY (or GROQ_API) in .env.'
            ]);
            return;
        }

        $messages = [
            [
                'role' => 'user',
                'content' => $user_query,
            ]
        ];

        if ($config['system_prompt'] !== '') {
            array_unshift($messages, [
                'role' => 'system',
                'content' => $config['system_prompt'],
            ]);
        }

        $models = array_values(array_unique(array_filter(array_merge([$config['model']], $config['fallback_models']))));
        $attempts = max(1, $config['max_retries'] + 1);

        $last_status = 500;
        $last_message = 'Unable to reach the Groq API right now.';

        for ($attempt = 0; $attempt < $attempts; $attempt++) {
            $model = $models[min($attempt, count($models) - 1)];
            $payload = [
                'model' => $model,
                'messages' => $messages,
                'temperature' => $config['temperature'],
                'max_tokens' => $config['max_tokens'],
                'top_p' => $config['top_p'],
                'stream' => false,
            ];

            $result = $this->executeGroqRequest($config, $payload);
            $http_code = $result['http_code'];
            $groq_response = $result['body'];
            $curl_error = $result['curl_error'];
            $headers = $result['headers'];

            if ($curl_error !== '') {
                $last_status = 502;
                $last_message = 'Network error while contacting Groq: ' . $curl_error;

                if ($attempt < $attempts - 1) {
                    $sleep_seconds = $this->resolveRetryDelaySeconds($attempt, $headers, false);
                    usleep((int)round($sleep_seconds * 1000000));
                    continue;
                }

                break;
            }

            $decoded = json_decode($groq_response, true);

            if ($http_code === 200 && isset($decoded['choices'][0]['message']['content'])) {
                echo $groq_response;
                return;
            }

            $error_message = $decoded['error']['message'] ?? ('Groq request failed with HTTP code: ' . $http_code);
            $is_rate_limited = $http_code === 429 || stripos($error_message, 'rate limit') !== false;
            $is_retryable = in_array($http_code, [408, 409, 425, 429, 500, 502, 503, 504], true);

            $last_status = $is_rate_limited ? 429 : ($http_code > 0 ? $http_code : 500);
            $last_message = $error_message;

            if ($attempt < $attempts - 1 && $is_retryable) {
                $sleep_seconds = $this->resolveRetryDelaySeconds($attempt, $headers, $is_rate_limited);
                usleep((int)round($sleep_seconds * 1000000));
                continue;
            }

            break;
        }

        http_response_code($last_status);
        echo json_encode([
            'error' => $last_message,
            'hint' => 'If you keep getting 429, lower max_tokens, use a lighter model, or wait for quota reset.',
            'status' => $last_status,
        ]);
    }

    private function getGroqConfig() {
        $fallback_raw = $_ENV['GROQ_FALLBACK_MODELS'] ?? '';
        $fallback_models = array_values(array_filter(array_map('trim', explode(',', $fallback_raw))));

        $max_retries = (int)($_ENV['GROQ_MAX_RETRIES'] ?? 2);
        $max_retries = max(0, min($max_retries, 5));

        $max_tokens = (int)($_ENV['GROQ_MAX_TOKENS'] ?? 512);
        $max_tokens = max(64, min($max_tokens, 4096));

        $temperature = (float)($_ENV['GROQ_TEMPERATURE'] ?? 0.7);
        $temperature = max(0.0, min($temperature, 2.0));

        $top_p = (float)($_ENV['GROQ_TOP_P'] ?? 1.0);
        $top_p = max(0.0, min($top_p, 1.0));

        return [
            'api_url' => trim($_ENV['GROQ_API_URL'] ?? 'https://api.groq.com/openai/v1/chat/completions'),
            'api_key' => trim($_ENV['GROQ_API_KEY'] ?? ($_ENV['GROQ_API'] ?? '')),
            'model' => trim($_ENV['GROQ_MODEL'] ?? 'llama-3.1-8b-instant'),
            'fallback_models' => $fallback_models,
            'temperature' => $temperature,
            'max_tokens' => $max_tokens,
            'top_p' => $top_p,
            'max_retries' => $max_retries,
            'request_timeout' => max(5, (int)($_ENV['GROQ_REQUEST_TIMEOUT'] ?? 45)),
            'connect_timeout' => max(3, (int)($_ENV['GROQ_CONNECT_TIMEOUT'] ?? 10)),
            'system_prompt' => trim($_ENV['GROQ_SYSTEM_PROMPT'] ?? ''),
        ];
    }

    private function executeGroqRequest($config, $payload) {
        $response_headers = [];

        $ch = curl_init($config['api_url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $config['api_key'],
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, $config['request_timeout']);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $config['connect_timeout']);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header_line) use (&$response_headers) {
            $len = strlen($header_line);
            $parts = explode(':', $header_line, 2);

            if (count($parts) === 2) {
                $name = strtolower(trim($parts[0]));
                $value = trim($parts[1]);
                $response_headers[$name] = $value;
            }

            return $len;
        });

        $body = curl_exec($ch);
        $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        return [
            'http_code' => $http_code,
            'body' => $body ?: '',
            'curl_error' => $curl_error,
            'headers' => $response_headers,
        ];
    }

    private function resolveRetryDelaySeconds($attempt, $headers, $is_rate_limited) {
        if (!empty($headers['retry-after'])) {
            $retry_after = trim($headers['retry-after']);

            if (is_numeric($retry_after)) {
                return max(1.0, (float)$retry_after);
            }

            $retry_at = strtotime($retry_after);
            if ($retry_at !== false) {
                return max(1.0, (float)($retry_at - time()));
            }
        }

        $base = $is_rate_limited ? 1.5 : 0.8;
        $jitter = random_int(0, 500) / 1000;

        return min(15.0, ($base * pow(2, $attempt)) + $jitter);
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

    private function validateUpdateFormulationData($data) {
        $errors = [];
        
        // Ensure the formulation_id is present and valid
        if (empty($data['formulation_id']) || !is_numeric($data['formulation_id']) || $data['formulation_id'] <= 0) {
            $errors[] = "A valid formulation ID is required.";
        }
        
        // Validate medicine_name
        if (empty($data['medicine_name']) || !is_numeric($data['medicine_name']) || $data['medicine_name'] <= 0) {
            $errors[] = "A valid medicine name is required.";
        }
    
        // Validate material_name
        if (empty($data['material_name']) || !is_numeric($data['material_name']) || $data['material_name'] <= 0) {
            $errors[] = "A valid material name is required.";
        }
    
        // Validate quantity_required
        if (!isset($data['quantity_required']) || $data['quantity_required'] === '') {
            $errors[] = "Quantity required is mandatory.";
        } elseif (!is_numeric($data['quantity_required']) || $data['quantity_required'] <= 0) {
            $errors[] = "Quantity required must be a positive number.";
        }
    
        // Validate unit
        if (empty($data['unit'])) {
            $errors[] = "A valid unit is required.";
        }
    
        // Validate description (optional but checked for completeness)
        if (empty($data['description'])) {
            $errors[] = "Description is required.";
        }
    
        return $errors;
    }
}