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
    public function index()
    {
        // Cek login admin
        if (!Session::has('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Silahkan login terlebih dahulu.');
        }

        try {
            // Ambil semua dokumen rumah_kos
            $docs = $this->firebase->fetchCollection('rumah_kos');
            log::info('Fetched rumah_kos docs: ' . print_r($docs, true));

            $rumahKos = [];

            foreach ($docs as $doc) {
                $fields = $doc['fields'] ?? [];
                $docId = basename($doc['name']); // <-- ambil dokumen ID dari path

                $rumahKos[] = [
                    'id'           => $docId, // sekarang ini benar ID Firebase
                    'nama_kos'     => $fields['nama_kos']['stringValue'] ?? '',
                    'lokasi'       => $fields['lokasi']['stringValue'] ?? '',
                    'jumlah_kamar' => isset($fields['jumlah_kamar']['integerValue']) ? (int)$fields['jumlah_kamar']['integerValue'] : 0,
                    'foto'         => $fields['foto']['stringValue'] ?? null,
                    'created_at'   => $fields['created_at']['stringValue'] ?? '',
                    'updated_at'   => $fields['updated_at']['stringValue'] ?? '',
                ];
            }

            return view('rumah_kos.index', compact('rumahKos'));
        } catch (\Exception $e) {
            Log::error('Firebase fetch rumah_kos error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Gagal mengambil data rumah kos.');
        }
    }


    public function store(Request $request)
    {
        // Cek login admin
        if (!Session::has('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Silahkan login terlebih dahulu.');
        }

        // Validasi input
        $request->validate([
            'nama_kos'      => 'required|string|max:255',
            'lokasi'        => 'required|string|max:255',
            'jumlah_kamar'  => 'required|integer|min:1',
            'foto'          => 'nullable|file|mimes:jpg,jpeg,png|max:2048'
        ]);

        try {
            // Upload foto jika ada
            $fotoUrl = '';
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $remotePath = 'foto_kos/' . time() . '_' . Str::random(10) . '.' . $file->extension();
                $fotoUrl = $this->firebase->uploadFile($file->getRealPath(), $remotePath);
            }

            // Generate ID dokumen: RK_ + random 12 karakter
            $idDoc = 'RK_' . Str::upper(Str::random(12));

            // Siapkan data kos
            $fields = [
                'nama_kos'     => $request->nama_kos,
                'lokasi'       => $request->lokasi,
                'jumlah_kamar' => (int)$request->jumlah_kamar,
                'foto'         => $fotoUrl,
                'created_at'   => now()->toDateTimeString(),
                'updated_at'   => now()->toDateTimeString(),
            ];

            // Simpan dokumen ke Firebase
            $this->firebase->createDocument('rumah_kos', $fields, $idDoc);

            return redirect()->route('rumah-kos.create')->with('success', 'Berhasil menambahkan kos baru!');
        } catch (\Exception $e) {
            Log::error('Firebase REST error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan kos: ' . $e->getMessage());
        }
    }

    //detail rumah kos
    public function detail($idDoc)
    {
        if (!Session::has('admin_logged_in')) {
            return redirect()->route('dashboard');
        }

        try {
            $firebase = app(FirebaseRestService::class);

            $kosData = $firebase->fetchDocument('rumah_kos', $idDoc);

            $fields = $kosData['fields'] ?? [];
            $kos = [
                'idDoc' => $idDoc,
                'nama_kos' => $fields['nama_kos']['stringValue'] ?? 'Kos tanpa nama',
                'lokasi' => $fields['lokasi']['stringValue'] ?? '',
                'jumlah_kamar' => (int)($fields['jumlah_kamar']['integerValue'] ?? 0),
                'foto' => $fields['foto']['stringValue'] ?? null,
            ];

            return view('kamar_kos.data_kamar', compact('kos'));
        } catch (\Exception $e) {
            Log::error("Fetch detail kos error: " . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Gagal mengambil data kos.');
        }
    }
    // Tampilkan form edit
    public function edit($id)
    {
        if (!Session::has('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Silahkan login terlebih dahulu.');
        }

        try {
            $doc = $this->firebase->fetchDocument('rumah_kos', $id);
            $fields = $doc['fields'] ?? [];

            $kos = [
                'id'           => $id,
                'nama_kos'     => $fields['nama_kos']['stringValue'] ?? '',
                'lokasi'       => $fields['lokasi']['stringValue'] ?? '',
                'jumlah_kamar' => isset($fields['jumlah_kamar']['integerValue']) ? (int)$fields['jumlah_kamar']['integerValue'] : 0,
                'foto'         => $fields['foto']['stringValue'] ?? null,
            ];

            return view('rumah_kos.edit', compact('kos'));
        } catch (\Exception $e) {
            Log::error('Fetch kos for edit error: ' . $e->getMessage());
            return redirect()->route('rumah_kos.index')->with('error', 'Gagal mengambil data kos.');
        }
    }

    // Proses update
    public function update(Request $request, $id)
    {
        if (!Session::has('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Silahkan login terlebih dahulu.');
        }

        $request->validate([
            'nama_kos'     => 'required|string|max:255',
            'lokasi'       => 'required|string|max:255',
            'jumlah_kamar' => 'required|integer|min:1',
            'foto'         => 'nullable|file|mimes:jpg,jpeg,png|max:2048'
        ]);

        try {
            $doc = $this->firebase->fetchDocument('rumah_kos', $id);
            $fields = $doc['fields'] ?? [];

            // Upload foto baru jika ada
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $remotePath = 'foto_kos/' . time() . '_' . Str::random(10) . '.' . $file->extension();
                $fotoUrl = $this->firebase->uploadFile($file->getRealPath(), $remotePath);
            } else {
                $fotoUrl = $fields['foto']['stringValue'] ?? '';
            }

            // Update data
            $updateData = [
                'nama_kos'     => $request->nama_kos,
                'lokasi'       => $request->lokasi,
                'jumlah_kamar' => (int)$request->jumlah_kamar,
                'foto'         => $fotoUrl,
                'updated_at'   => now()->toDateTimeString(),
            ];

            $this->firebase->updateDocument('rumah_kos', $id, $updateData);

            return redirect()->route('rumah_kos.index')->with('success', 'Data rumah kos berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Update kos error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui data kos.');
        }
    }
    // Proses hapus rumah kos
    public function destroy($id)
    {
        if (!Session::has('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Silahkan login terlebih dahulu.');
        }

        try {
            // Hapus dokumen dari Firebase
            $this->firebase->deleteDocument('rumah_kos', $id);

            return redirect()->route('rumah_kos.index')->with('success', 'Data rumah kos berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Hapus kos error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus rumah kos.');
        }
    }
}
