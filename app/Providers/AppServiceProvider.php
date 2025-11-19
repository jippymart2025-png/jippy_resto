<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//        setcookie('XSRF-TOKEN-AK', bin2hex(env('FIREBASE_APIKEY')), time() + 3600, "/");
//        setcookie('XSRF-TOKEN-AD', bin2hex(env('FIREBASE_AUTH_DOMAIN')), time() + 3600, "/");
//        setcookie('XSRF-TOKEN-DU', bin2hex(env('FIREBASE_DATABASE_URL')), time() + 3600, "/");
//        setcookie('XSRF-TOKEN-PI', bin2hex(env('FIREBASE_PROJECT_ID')), time() + 3600, "/");
//        setcookie('XSRF-TOKEN-SB', bin2hex(env('FIREBASE_STORAGE_BUCKET')), time() + 3600, "/");
//        setcookie('XSRF-TOKEN-MS', bin2hex(env('FIREBASE_MESSAAGING_SENDER_ID')), time() + 3600, "/");
//        setcookie('XSRF-TOKEN-AI', bin2hex(env('FIREBASE_APP_ID')), time() + 3600, "/");
//        setcookie('XSRF-TOKEN-MI', bin2hex(env('FIREBASE_MEASUREMENT_ID')), time() + 3600, "/");
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(['layouts.*'], function ($view) {
            $user = Auth::user();
            $vendor = null;
            $vendorId = null;
            $vendorUuid = null;

            if ($user) {
                $vendorUuid = $user->firebase_id ?? $user->_id ?? null;

                if (! empty($user->vendorID)) {
                    $vendorId = $user->vendorID;
                }

                if (! $vendorId && $vendorUuid) {
                    $vendor = Vendor::where('author', $vendorUuid)->first();
                } elseif ($vendorId) {
                    $vendor = Vendor::where('id', $vendorId)->first();
                }
            }

            $settings = Setting::whereIn('document_name', [
                'document_verification_settings',
                'DineinForRestaurant',
                'AdminCommission',
                'restaurant',
                'globalSettings',
            ])->get()->keyBy('document_name');

            $documentSettings = $settings->get('document_verification_settings');
            $dineInSettings = $settings->get('DineinForRestaurant');
            $brandSettings = $settings->get('globalSettings');

            $view->with([
                'layoutUser' => $user,
                'layoutVendor' => $vendor,
                'layoutVendorUuid' => $vendorUuid,
                'layoutDocumentVerificationRequired' => (bool) data_get($documentSettings?->fields, 'isRestaurantVerification', false),
                'layoutDineInEnabled' => (bool) data_get($dineInSettings?->fields, 'isEnabled', false),
                'layoutBranding' => $brandSettings?->fields ?? [],
            ]);
        });
    }
}
