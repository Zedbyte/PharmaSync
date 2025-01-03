<?php

namespace App\Controllers;

use App\Models\MedicineBatch;
use App\Models\Order;
use App\Models\Inventory;

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

        $medicineBatchObject = new MedicineBatch();
        $medicineBatchData = $medicineBatchObject->getMedicineBatchListOrderedByExpiry();

        $orderObject = new Order();
        $orderData = $orderObject->getRecentOrders();

        
        $inventoryObject = new Inventory();
        $totalOrders = $inventoryObject->getCurrentMonthOrdersWithComparison();
        $purchaseFrequency = $inventoryObject->getPurchaseFrequencyForCurrentAndPreviousMonth();
        $salesPerformance = $inventoryObject->getSalesPerformanceForCurrentAndPreviousMonth();
        $productionEfficiency = $inventoryObject->getCurrentMonthProductionEfficiencyWithComparison();

        // var_dump($purchaseFrequency);exit;

        echo $this->twig->render('dashboard.html.twig', [
            'ASSETS_URL' => ASSETS_URL,
            'medicineBatchData' => $medicineBatchData,
            'orderData' => $orderData,
            'totalOrders' => $totalOrders,
            'purchaseFrequency' => $purchaseFrequency,
            'salesPerformance' => $salesPerformance,
            'productionEfficiency' => $productionEfficiency
        ]);
    }
}
