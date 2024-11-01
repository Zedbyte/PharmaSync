<?php

namespace App\Controllers;

require_once __DIR__ . '/../../config/config.php';

class DashboardController extends BaseController
{
    protected $twig;

    public function __construct($twig)
    {
        parent::__construct();  // Ensures session management and authentication
        $this->twig = $twig;
    }

    public function display()
    {
        echo $this->twig->render('dashboard.html.twig', ['ASSETS_URL' => ASSETS_URL]);
    }
}
