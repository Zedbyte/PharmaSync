<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Batch;
use App\Models\Rack;

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
        $medicineData = $medicineObject->getAllMedicines();

        echo $this->twig->render('medicine-list.html.twig', [
            'ASSETS_URL' => ASSETS_URL,
            'medicineData' => $medicineData
        ]);
    }

    public function addMedicine() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            
            // Validate the data
            // $errors = $this->validateBatchData($data);
            // if (!empty($errors)) {
            //     $_SESSION['batch_errors'] = $errors;
            //     // Redirect to the same route with GET (to prevent resubmission)
            //     header('Location: /add-medicine');
            //     exit;
            // }
            
            // Save Medicine information
            $medicineObject = new Medicine();
            foreach ($data['medicine_type'] as $index => $medicineType) {
                $medicineObject->save([
                    'medicine_type' => $medicineType,
                    'material_name' => $data['material_name'][$index],
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

        $errors = isset($_SESSION['batch_errors']) ? $_SESSION['batch_errors'] : [];
    
        // Render the template with errors, if any
        $this->display($errors);
        
        // Clear the errors from session after they are displayed
        unset($_SESSION['batch_errors']);
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
            $api_key =  $_ENV['GROQ_API']; // Replace with your actual GROQ API key
        
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
}