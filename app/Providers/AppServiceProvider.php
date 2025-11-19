<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Illuminate\Support\Facades\View;
use App\Services\FirebaseRestService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('firebase.firestore', function () {
            $factory = (new Factory)->withServiceAccount(config('firebase.projects.app.credentials'));
            return $factory->createFirestore();
        });
    }



    public function boot()
    {
        View::composer('layouts.sidebar', function ($view) {
            $kosList = [];

            if (Session::has('admin_logged_in')) {
                try {
                    $firebase = app(FirebaseRestService::class);
                    $kosDocuments = $firebase->fetchCollection('rumah_kos');

                    foreach ($kosDocuments as $kosDoc) {
                        $fields = $kosDoc['fields'] ?? [];
                        $kosList[] = [
                            'id_kos' => basename($kosDoc['name'] ?? ''),
                            'nama_kos' => $fields['nama_kos']['stringValue'] ?? 'Kos tanpa nama',
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error("Fetch kos error: " . $e->getMessage());
                }
            }

            // Minimal satu placeholder supaya Blade tidak error
            if (empty($kosList)) {
                $kosList[] = [
                    'id_kos' => 'kos_placeholder',
                    'nama_kos' => 'Kos tanpa nama',
                ];
            }

            $view->with('kosList', $kosList);
        });
    }
}
