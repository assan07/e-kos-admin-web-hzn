<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Helpers\FirebaseHelper;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            $adminEmail = session('admin_email'); // ambil dari session login
            $adminData = null;

            if ($adminEmail) {
                $adminData = FirebaseHelper::getAdminByEmail($adminEmail);
            }

            $view->with('admin', $adminData);
        });
    }

    public function register() {}
}
