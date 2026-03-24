<?php

namespace Modules\Shopify\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Shopify\Services\SetupService;

class SetupController extends BaseController
{
    public function store(Request $request)
    {
        $settings = $request->settings;

        if ($settings) {
            foreach ($settings as $key => $value) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $key],
                    ['value' => is_array($value) ? json_encode($value) : $value]
                );
            }
        }

        if (isset($request->is_active)) {
            Addon::where('uuid', $request->uuid)->update(['is_active' => $request->is_active]);
        }

        // Run module migrations
        $setupService = new SetupService();
        $setupService->index();

        return redirect('/admin/addons')->with(
            'status', [
                'type' => 'success',
                'message' => __('Shopify module installed successfully!')
            ]
        );
    }

    public function update(Request $request)
    {
        $settings = $request->settings;

        if ($settings) {
            foreach ($settings as $key => $value) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $key],
                    ['value' => is_array($value) ? json_encode($value) : $value]
                );
            }
        }

        if (isset($request->is_active)) {
            Addon::where('uuid', $request->uuid)->update(['is_active' => $request->is_active]);
        }

        return redirect('/admin/addons')->with(
            'status', [
                'type' => 'success',
                'message' => __('Shopify settings updated successfully!')
            ]
        );
    }
}
