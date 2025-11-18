<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('firebase.firestore', function () {
            $factory = (new Factory)->withServiceAccount(config('firebase.projects.app.credentials'));
            return $factory->createFirestore();
        });
    }

    public function boot(): void {}
}
