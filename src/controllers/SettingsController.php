<?php

namespace App\Controllers;
require_once __DIR__ . '/../../config/config.php';

class SettingsController {
    protected $twig;

    // Constructor to pass Twig environment for rendering templates
    public function __construct($twig) {
        $this->twig = $twig;
    }

    function display() {
        echo $this->twig->render('settings.html.twig', ['ASSETS_URL' => ASSETS_URL]);
    }
}