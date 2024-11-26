<?php

namespace App\Controllers;

require_once __DIR__ . '/../../config/config.php';

class ErrorController extends BaseController
{
    protected $twig;

    public function __construct($twig)
    {
        parent::__construct();  // Ensures session management and authentication
        $this->twig = $twig;
    }

    public function display($errorCode)
    {
        echo $this->twig->render('error.html.twig',
        [
            'ASSETS_URL' => ASSETS_URL,
            'error_code' => $errorCode
        ]);
    }
}
