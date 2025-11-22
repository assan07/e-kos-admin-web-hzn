<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Services\FirestoreService;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login-page');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $fs = app(FirestoreService::class);
        $response = $fs->request('get', 'admins');
        $admins = $response['data']['documents'] ?? [];

        if (empty($admins)) {
            return back()->withErrors([
                'login' => 'Akun admin belum tersedia dalam sistem'
            ]);
        }

        foreach ($admins as $adminDoc) {
            $fields = $adminDoc['fields'];
            $email = $fields['email']['stringValue'];
            $passwordHash = $fields['password']['stringValue'];

            if ($email === $request->email && Hash::check($request->password, $passwordHash)) {

                Session::put('admin_logged_in', [
                    'id' => basename($adminDoc['name']),
                    'nama' => $fields['nama']['stringValue'],
                    'email' => $email
                ]);


                return redirect()->route('dashboard');
            }
        }

        return back()->withErrors(['login' => 'Email atau password salah']);
    }



    public function logout()
    {
        Session::forget('admin_logged_in');
        return redirect()->route('login');
    }
}
