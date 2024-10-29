<?php

namespace App\Controllers;
require_once __DIR__ . '/../../config/config.php';

class DashboardController {
    protected $twig;

    // Constructor to pass Twig environment for rendering templates
    public function __construct($twig) {
        $this->twig = $twig;
    }

    function display() {
        echo $this->twig->render('dashboard.html.twig', ['BASE_URL' => BASE_URL]);
    }
}