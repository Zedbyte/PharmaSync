<?php

namespace App\controllers;

class DashboardController {
    protected $twig;

    // Constructor to pass Twig environment for rendering templates
    public function __construct($twig) {
        $this->twig = $twig;
    }

    function display() {
        echo $this->twig->render('dashboard.html.twig');
    }
}