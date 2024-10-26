<?php

namespace App\Controllers;

class SettingsController {
    protected $twig;

    // Constructor to pass Twig environment for rendering templates
    public function __construct($twig) {
        $this->twig = $twig;
    }

    function display() {
        echo $this->twig->render('settings.html.twig');
    }
}