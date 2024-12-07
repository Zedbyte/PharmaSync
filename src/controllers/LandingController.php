<?php
namespace App\Controllers;

require_once __DIR__ . '/../../config/config.php';
require_once 'BaseController.php';

class LandingController extends BaseController
{
    protected $twig;

    public function __construct($twig)
    {
        parent::__construct();
        $this->twig = $twig;
    }

    public function showLandingPage()
    {
        // Render the landing page
        echo $this->twig->render('pages/landing/landing.html.twig', [
            'ASSETS_URL' => ASSETS_URL, // Pass constants like asset paths if needed
        ]);
    }

    public function showAboutPage()
    {
        echo $this->twig->render('pages/landing/about.html.twig', [
            'ASSETS_URL' => '/public/assets',
        ]);
    }
    public function showClientsPage()
    {
        echo $this->twig->render('pages/landing/client.html.twig', [
            'ASSETS_URL' => '/public/assets',
        ]);
    }

    public function showContactUsPage()
    {
        echo $this->twig->render('pages/landing/contactus.html.twig', [
            'ASSETS_URL' => '/public/assets', // Provide the correct assets URL
        ]);
    }
}