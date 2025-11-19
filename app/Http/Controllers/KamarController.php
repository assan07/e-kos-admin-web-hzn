<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Services\FirebaseRestService;
use Illuminate\Support\Str;


class KamarController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseRestService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * LIST KAMAR
     */
    public function index($idKos)
    {
        try {
            $data = $this->firebase->fetchCollection("rumah_kos/$idKos/kamar");

            $kamarList = [];
            foreach ($data as $doc) {
                $field = $doc['fields'] ?? [];

                $kamarList[] = [
                    'id_kamar' => basename($doc['name']),
                    'nama_kamar' => $field['nama_kamar']['stringValue'] ?? '-',
                    'alamat' => $field['alamat']['stringValue'] ?? '-',
                    'no_kamar' => $field['no_kamar']['stringValue'] ?? '-',
                    'foto' => $field['foto']['stringValue'] ?? null,
                    'harga' => (int) ($field['harga']['integerValue'] ?? 0),
                    'fasilitas' => $field['fasilitas']['stringValue'] ?? '',
                    'status' => $field['status']['stringValue'] ?? 'kosong',
                    'jumlah_penghuni' => (int) ($field['jumlah_penghuni']['integerValue'] ?? 0),
                ];
            }

            return response()->json($kamarList, 200);
        } catch (\Exception $e) {
            Log::error("Fetch kamar error: " . $e->getMessage());
            return response()->json(['error' => 'Gagal mengambil kamar'], 500);
        }
    }


    /**
     * SIMPAN KAMAR BARU
     */
    public function store(Request $req, $idKos)
    {
        if (!Session::has('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Silahkan login terlebih dahulu.');
        }
        try {
            // VALIDASI
            $req->validate([
                'nama_kamar' => 'required|string',
                'alamat'     => 'required|string',
                'no_kamar'   => 'required|string',
                'harga'      => 'required|numeric',
                'status'     => 'required|string',
                'fasilitas'  => 'nullable|string',
                'foto'       => 'nullable|file|image|max:2048',
            ]);

            // UPLOAD FOTO (jika ada)
            $fotoUrl = '';
            if ($req->hasFile('foto')) {
                $file = $req->file('foto');
                $remotePath = 'foto_kamar/' . time() . '_' . Str::random(10) . '.' . $file->extension();
                $fotoUrl = $this->firebase->uploadFile($file->getRealPath(), $remotePath);
            }

            // PROSES FASILITAS: string -> array (trim, remove empty)
            $fasilitas = $req->fasilitas;

            if (is_string($fasilitas)) {
                $fasilitas = array_map('trim', explode(',', $fasilitas));
            }


            // Build Firestore payload (fields)
            $dataFirestore = [
                'nama_kamar' => $req->nama_kamar,
                'alamat' => $req->alamat,
                'no_kamar' => $req->no_kamar,
                'harga' =>  (int)$req->harga,
                'status' =>  $req->status,
                'fasilitas' => $fasilitas,
                'jumlah_penghuni' =>  0,
                'foto' =>  $fotoUrl,
                'created_at' => $this->firebase->fsTimestamp(now()),
                'updated_at' => $this->firebase->fsTimestamp(now()),

            ];


            // Simpan dokumen ke subcollection rumah_kos/{idKos}/kamar
            // Gunakan docId unik (atau biarkan Firestore generate dengan endpoint berbeda)
            $docId = "KMR-" . uniqid();
            $this->firebase->createDocument("rumah_kos/{$idKos}/kamar", $dataFirestore, $docId);

            return redirect()->route('kamar.index')->with('success', 'Kamar berhasil ditambahkan!');
        } catch (\Illuminate\Validation\ValidationException $ve) {
            // kirim pesan validasi
            $messages = $ve->validator->errors()->all();

            Log::warning("Validasi tambah kamar gagal: " . json_encode($messages));
            return redirect()->back()->with('error', 'Validasi gagal: ' . implode(', ', $messages))->withInput();
        } catch (\Exception $e) {
            // Log lengkap untuk debugging (pesan + trace)
            Log::error("Tambah Kamar Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            // Jika error karena foreach di tempat lain, log variabel pendukung
            return redirect()->back()->with('error', 'Gagal menambahkan kos: ' . $e->getMessage());
        }
    }


    /**
     * DETAIL KAMAR
     */
    public function show($idKamar)
    {
        try {
            $doc = $this->firebase->fetchDocumentById("kamar", $idKamar);

            if (!$doc) {
                return response()->json(['error' => 'Kamar tidak ditemukan'], 404);
            }

            return response()->json($doc, 200);
        } catch (\Exception $e) {
            Log::error("Detail kamar error: " . $e->getMessage());
            return response()->json(['error' => 'Gagal mengambil detail kamar'], 500);
        }
    }



    /**
     * UPDATE KAMAR
     */
    public function update(Request $req, $idKamar)
    {
         $fasilitas = $req->fasilitas;

            if (is_string($fasilitas)) {
                $fasilitas = array_map('trim', explode(',', $fasilitas));
            }
        try {
            // Validasi dasar
            $req->validate([
                'nama_kamar' => 'required|string',
                'alamat' => 'required|string',
                'no_kamar' => 'required|string',
                'harga' => 'required|numeric',
                'status' => 'required|string',
                'fasilitas' => 'nullable|array',
                'foto' => 'nullable|image|max:2048'
            ]);

            // Fetch data lama
            $oldDoc = $this->firebase->fetchDocumentById("kamar", $idKamar);
            if (!$oldDoc) {
                return response()->json(['error' => 'Kamar tidak ditemukan'], 404);
            }

            $fotoUrl = $oldDoc['foto'] ?? null;

            // Jika upload foto baru
            if ($req->hasFile('foto')) {
                $upload = $this->firebase->uploadImage($req->file('foto'), "kamar/$idKamar");

                if (!$upload['success']) {
                    return response()->json(['error' => 'Gagal upload foto baru'], 500);
                }

                $fotoUrl = $upload['url'];
            }

            // Format data
            $dataFirestore = [
                'nama_kamar' => $req->nama_kamar,
                'alamat' => $req->alamat,
                'no_kamar' => $req->no_kamar,
                'harga' => (int)$req->harga,
                'status' => $req->status,
                'fasilitas' => $fasilitas,  // array biasa
                'foto' => $fotoUrl,
                'updated_at' => now()->toRfc3339String(), // timestamp raw
            ];

            // Update Firestore
            $res = $this->firebase->updateDocument("kamar/$idKamar", $dataFirestore);//masih errror di sini

            if (!$res['success']) {
                return response()->json(['error' => 'Gagal update kamar: ' . $res['message']], 500);
            }

            return response()->json(['success' => true, 'message' => 'Kamar berhasil diperbarui']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    /**
     * DELETE KAMAR
     */
    public function destroy($idKamar)
    {
        try {
            $this->firebase->deleteDocument("kamar", $idKamar);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Delete kamar error: " . $e->getMessage());
            return response()->json(['error' => 'Gagal menghapus kamar'], 500);
        }
    }
}
