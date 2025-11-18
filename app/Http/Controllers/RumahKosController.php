<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Firestore\FirestoreClient;

class RumahKosController extends Controller
{
    protected $firestore;

    public function __construct()
    {
        $this->firestore = new FirestoreClient([
            'projectId' => env('FIREBASE_PROJECT_ID'),
        ]);
    }

    public function store(Request $request)
    {
        // Basic validation
        $request->validate([
            'nama' => 'required|string',
            'lokasi' => 'required|string',
            'jumlah_kamar' => 'required|integer|min:0',
            'foto' => 'nullable|string',
        ]);

        // Autogenerate dokumen ID dari Firestore
        $docRef = $this->firestore->collection('rumah_kos')->newDocument();

        $data = [
            'nama' => $request->nama,
            'lokasi' => $request->lokasi,
            'jumlah_kamar' => (int) $request->jumlah_kamar,
            'foto' => $request->foto ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $docRef->set($data);

        return redirect()->back()->with('success', 'Rumah kos berhasil ditambahkan.');
    }
}
