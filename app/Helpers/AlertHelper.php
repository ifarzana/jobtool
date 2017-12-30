<?php

namespace App\Helpers;

use App\Managers\Alert\AlertManager;
use Illuminate\Support\Facades\App;

class AlertHelper
{
    /**
     * Returns the active alerts from the alert manager
     *
     * @return array
     */
    static function getAlerts()
    {
        $alertManager = $aclManager = App::make(AlertManager::class);

        $alerts = $alertManager->getAlerts();

        return $alerts;
    }
    
}