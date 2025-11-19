<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseRestService;

class DashboardController extends Controller
{
    public function index(FirebaseRestService $firebase)
    {
        $kosDocuments = $firebase->fetchCollection('rumah_kos');
        $kosList = [];
        foreach ($kosDocuments as $kosDoc) {
            $kosList[] = [
                'id_kos' => basename($kosDoc['name']),
                'nama_kos' => $kosDoc['fields']['nama_kos']['stringValue'] ?? 'Kos tanpa nama',
            ];
        }

        return view('admin.dashboard', compact('kosList'));
    }
}
