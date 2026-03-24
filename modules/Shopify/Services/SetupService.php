<?php

namespace Modules\Shopify\Services;

use Illuminate\Support\Facades\Artisan;

class SetupService
{
    public function index()
    {
        $migrateOutput = Artisan::call('module:migrate', [
            'module' => 'Shopify',
        ]);
    }
}
