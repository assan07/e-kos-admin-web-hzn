<?php

use Illuminate\Support\Facades\Route;
use App\Services\FirestoreService;

Route::get('/', function() {
    return view('auth.login-page'); 
})->name('login');

// Route::get('/dashboard', function() {
//     return view('admin.dashboard'); 
// })->name('dashboard');


// debug route untuk mengetes koneksi ke Firestore
// Route::get('/firestore-test', function(FirestoreService $fs) {
//     return $fs->request('get', 'admins');
// });
