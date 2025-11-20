<?php

use Illuminate\Support\Facades\Route;
use App\Services\FirestoreService;

use Kreait\Firebase\Factory;

Route::get('/test-firebase', function () {
    $path = storage_path('app/firebase/service-account.json');

    if (!file_exists($path)) {
        return 'Service account JSON tidak ditemukan!';
    }

    try {
        $factory = (new Factory)->withServiceAccount($path);
        $firebase = $factory->createStorage();
        $bucket = $firebase->getBucket('gs://e-kos-9b7ee');

        return 'Credential valid, bucket siap digunakan!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});


Route::get('/', function () {
    return view('auth.login-page');
})->name('login');

// Route::get('/dashboard', function() {
//     return view('admin.dashboard'); 
// })->name('dashboard');


// debug route untuk mengetes koneksi ke Firestore
// Route::get('/firestore-test', function(FirestoreService $fs) {
//     return $fs->request('get', 'admins');
// });