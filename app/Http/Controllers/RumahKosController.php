<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Services\FirebaseRestService;

class RumahKosController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseRestService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function create()
    {
        if (!Session::has('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Silahkan login terlebih dahulu.');
        }

        return view('rumah_kos.create');
    }

    public function store(Request $request)
    {
        if (!Session::has('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Silahkan login terlebih dahulu.');
        }

        $request->validate([
            'nama_kos'      => 'required|string|max:255',
            'lokasi'        => 'required|string|max:255',
            'jumlah_kamar'  => 'required|integer|min:1',
            'foto'          => 'nullable|file|mimes:jpg,jpeg,png|max:2048'
        ]);


        try {
            $fotoUrl = '';
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $remotePath = 'foto_kos/' . time() . '_' . Str::random(10) . '.' . $file->extension();
                $fotoUrl = $this->firebase->uploadFile($file->getRealPath(), $remotePath);
            }

            $idKos = 'KOS-' . Str::upper(Str::random(6));

            $fields = [
                'id_kos'       => $idKos,
                'nama_kos'     => $request->nama_kos,
                'lokasi'       => $request->lokasi,
                'jumlah_kamar' => (int)$request->jumlah_kamar,
                'foto'         => $fotoUrl,
                'created_at'   => now()->toDateTimeString(),
                'updated_at'   => now()->toDateTimeString(),
            ];

            $this->firebase->createDocument('rumah_kos', $fields);

            return redirect()->route('rumah-kos.create')->with('success', 'Berhasil menambahkan kos baru!');
        } catch (\Exception $e) {
            Log::error('Firebase REST error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan kos: ' . $e->getMessage());
        }
    }

    //detail rumah kos
    public function detail($idKos)
    {
        if (!Session::has('admin_logged_in')) {
            return redirect()->route('dashboard');
        }

        try {
            $firebase = app(FirebaseRestService::class);

            $kosData = $firebase->fetchDocument('rumah_kos', $idKos);

            $fields = $kosData['fields'] ?? [];
            $kos = [
                'id_kos' => $idKos,
                'nama_kos' => $fields['nama_kos']['stringValue'] ?? 'Kos tanpa nama',
                'lokasi' => $fields['lokasi']['stringValue'] ?? '',
                'jumlah_kamar' => (int)($fields['jumlah_kamar']['integerValue'] ?? 0),
                'foto' => $fields['foto']['stringValue'] ?? null,
            ];

            return view('rumah_kos.detail', compact('kos'));
        } catch (\Exception $e) {
            Log::error("Fetch detail kos error: " . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Gagal mengambil data kos.');
        }
    }
}
