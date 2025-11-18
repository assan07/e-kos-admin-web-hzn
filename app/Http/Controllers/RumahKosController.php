<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RumahKosController extends Controller
{
    public function create()
    {
        return view('rumah_kos.create');
    }

    public function store(Request $request)
    {
        // VALIDASI
        $request->validate([
            'nama_kos'      => 'required|string|max:255',
            'lokasi'        => 'required|string|max:255',
            'jumlah_kamar'  => 'required|integer|min:1',
            'foto'          => 'nullable|string'
        ]);

        // Firestore API endpoint
        $projectId = env('FIREBASE_PROJECT_ID');
        $apiKey    = env('FIREBASE_API_KEY');
        $url       = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/rumah_kos?key={$apiKey}";

        // Generate ID kos manual
        $idKos = 'KOS-' . Str::upper(Str::random(6));

        // Build request body
        $data = [
            "fields" => [
                "id_kos" => ["stringValue" => $idKos],
                "nama_kos" => ["stringValue" => $request->nama_kos],
                "lokasi" => ["stringValue" => $request->lokasi],
                "jumlah_kamar" => ["integerValue" => $request->jumlah_kamar],
                "foto" => ["stringValue" => $request->foto ?? ""],
                "created_at" => ["timestampValue" => now()->toISOString()],
                "updated_at" => ["timestampValue" => now()->toISOString()],
            ]
        ];

        // Send to Firestore
        $response = Http::post($url, $data);

        if ($response->successful()) {
            return redirect()
                ->route('rumah-kos.create')
                ->with('success', 'Berhasil menambahkan kos baru!');
        }

        return redirect()
            ->route('rumah-kos.create')
            ->with('error', 'Gagal menambahkan kos! Silakan coba lagi'.$response->body());
    }
}
