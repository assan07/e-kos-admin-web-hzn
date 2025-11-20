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
    public function index($idDoc)
    {
        try {
            // Ambil semua kamar
            $data = $this->firebase->fetchCollection("rumah_kos/$idDoc/kamar");

            // Ambil dokumen kos induk
            $kos = $this->firebase->fetchDocument('rumah_kos', $idDoc);
            if (!$kos) {
                return response()->json(['error' => 'Data kos tidak ditemukan'], 404);
            }

            // Ambil alamat dari dokumen induk
            $alamat = $kos['fields']['lokasi']['stringValue'] ?? 'Alamat tidak tersedia';
            Log::info("DEBUG Alamat kos untuk kamar: " . $alamat);

            $kamarList = [];
            foreach ($data as $doc) {
                $field = $doc['fields'] ?? [];
                $kamarList[] = [
                    'id_kamar' => basename($doc['name']),
                    'nama_kamar' => $field['nama_kamar']['stringValue'] ?? '-',
                    'alamat' => $alamat, // Ambil dari dokumen induk
                    'no_kamar' => $field['no_kamar']['stringValue'] ?? '-',
                    'foto' => $field['foto']['stringValue'] ?? null,
                    'harga' => (int) ($field['harga']['integerValue'] ?? 0),
                    'fasilitas' => $field['fasilitas']['arrayValue']['values'] ?? [],
                    'status' => $field['status']['stringValue'] ?? 'tersedia',
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
    public function store(Request $req, $idDoc)
    {
        try {
            // Ambil dokumen kos
            $kos = $this->firebase->fetchDocument('rumah_kos', $idDoc);
            if (!$kos) {
                return response()->json(['error' => 'Data kos tidak ditemukan'], 404);
            }

            $req->validate([
                'nama_kamar' => 'required|string',
                'no_kamar'   => 'required|string',
                'harga'      => 'required|numeric',
                'status'     => 'required|string',
                'fasilitas'  => 'nullable|string',
                'foto'       => 'nullable|file|image|max:2048',
            ]);

            // Upload foto jika ada
            $fotoUrl = '';
            if ($req->hasFile('foto')) {
                $file = $req->file('foto');
                $remotePath = 'foto_kamar/' . time() . '_' . Str::random(10) . '.' . $file->extension();
                $fotoUrl = $this->firebase->uploadFile($file->getRealPath(), $remotePath);
            }

            // Fasilitas
            $fasilitas = $req->fasilitas;
            if (is_string($fasilitas)) {
                $fasilitas = array_map('trim', explode(',', $fasilitas));
            }

            // Ambil alamat dari dokumen induk
            $alamat = $kos['fields']['lokasi']['stringValue'] ?? 'Alamat tidak tersedia';
            Log::info("DEBUG Alamat kos untuk kamar: " . $alamat);

            // Build payload Firestore
            $dataFirestore = [
                'nama_kamar'      => $req->nama_kamar,
                'alamat'          => $alamat, // optional, bisa dihapus kalau mau ambil selalu dari induk
                'no_kamar'        => $req->no_kamar,
                'harga'           => (int)$req->harga,
                'status'          => $req->status,
                'fasilitas'       => $fasilitas,
                'jumlah_penghuni' => 0,
                'foto'            => $fotoUrl,
                'created_at'      => $this->firebase->fsTimestamp(now()),
                'updated_at'      => $this->firebase->fsTimestamp(now()),
            ];

            $docId = "KMR-" . uniqid();
            $this->firebase->createDocument("rumah_kos/{$idDoc}/kamar", $dataFirestore, $docId);

            return response()->json(['message' => 'Kamar berhasil ditambahkan'], 200);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            $messages = $ve->validator->errors()->all();
            Log::warning("Validasi tambah kamar gagal: " . json_encode($messages));
            return response()->json(['error' => implode(', ', $messages)], 422);
        } catch (\Exception $e) {
            Log::error("Tambah Kamar Error: " . $e->getMessage());
            return response()->json(['error' => 'Gagal menambahkan kamar'], 500);
        }
    }

    /**
     * DETAIL KAMAR
     */
    public function showDetail($idDoc, $idKamar)
    {
        try {
            $doc = $this->firebase->fetchNestedDocument('rumah_kos', $idDoc, 'kamar', $idKamar);
            if (!$doc || !isset($doc['fields'])) {
                return back()->with('error', 'Kamar tidak ditemukan.');
            }

            $fields = $doc['fields'];
            $kos = $this->firebase->fetchDocument('rumah_kos', $idDoc);
            $alamat_kos = $kos['fields']['lokasi']['stringValue'] ?? 'Alamat tidak tersedia';

            // Gunakan helper mapKamarFieldsToDto
            $kamar = $this->firebase->mapKamarFieldsToDto($idKamar, $fields, $alamat_kos);

            return view('kamar_kos.update_kamar', compact('kamar', 'idDoc'));
        } catch (\Exception $e) {
            Log::error("ShowDetail kamar error: " . $e->getMessage());
            return back()->with('error', 'Gagal mengambil detail kamar.');
        }
    }


    /**
     * UPDATE KAMAR
     */
    public function update(Request $req, $idDoc, $idKamar)
    {
        try {
            $doc = $this->firebase->fetchNestedDocument('rumah_kos', $idDoc, 'kamar', $idKamar);
            if (!$doc) return back()->with('error', 'Kamar tidak ditemukan.');

            $fieldsOld = $doc['fields'] ?? [];

            // helper ambil value
            $getField = function ($fields, $key) {
                if (!isset($fields[$key])) return null;
                $f = $fields[$key];
                if (isset($f['stringValue'])) return $f['stringValue'];
                if (isset($f['integerValue'])) return (int)$f['integerValue'];
                return null;
            };

            $namaKamar = $req->filled('nama_kamar') ? $req->nama_kamar : $getField($fieldsOld, 'nama_kamar');
            $noKamar   = $req->filled('no_kamar') ? $req->no_kamar : $getField($fieldsOld, 'no_kamar');
            $harga     = $req->filled('harga_sewa') ? $req->harga_sewa : $getField($fieldsOld, 'harga');
            $status    = $req->filled('status') ? $req->status : $getField($fieldsOld, 'status');

            // fasilitas
            $fasilitasArr = [];
            if ($req->filled('fasilitas')) {
                $tmp = array_map('trim', explode(',', $req->fasilitas));
                $fasilitasArr = array_filter($tmp, fn($x) => $x !== '');
            } else {
                if (isset($fieldsOld['fasilitas']['arrayValue']['values'])) {
                    foreach ($fieldsOld['fasilitas']['arrayValue']['values'] as $v) {
                        if (isset($v['stringValue'])) $fasilitasArr[] = $v['stringValue'];
                    }
                }
            }

            // Foto
            $fotoUrl = $getField($fieldsOld, 'foto');
            if ($req->hasFile('foto_kamar')) {
                $file = $req->file('foto_kamar');
                $remotePath = 'foto_kamar/' . $idKamar . '_' . time() . '.' . $file->extension();
                $uploaded = $this->firebase->uploadFile($file->getRealPath(), $remotePath);
                if ($uploaded) $fotoUrl = $uploaded;
            }

            // Ambil alamat dari dokumen induk
            $kos = $this->firebase->fetchDocument('rumah_kos', $idDoc);
            $alamat = $kos['fields']['lokasi']['stringValue'] ?? 'Alamat tidak tersedia';

            $dataUpdate = [
                'nama_kamar' => $namaKamar,
                'no_kamar' => $noKamar,
                'harga' => (int)$harga,
                'status' => $status,
                'fasilitas' => $fasilitasArr,
                'foto' => $fotoUrl,
                'updated_at' => $this->firebase->fsTimestamp(now()),
            ];

            $this->firebase->updateDocument("rumah_kos/{$idDoc}/kamar", $idKamar, $dataUpdate);

            return back()->with('success', 'Data kamar berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error("Update kamar error: " . $e->getMessage());
            return back()->with('error', 'Gagal update kamar.');
        }
    }

    /**
     * DELETE KAMAR
     */
    public function destroy($idDoc, $idKamar)
    {
        try {
            $deleted = $this->firebase->deleteDocument("rumah_kos/{$idDoc}/kamar", $idKamar);
            if ($deleted) {
                return response()->json(['success' => true, 'message' => 'Kamar berhasil dihapus'], 200);
            }
            return response()->json(['success' => false, 'message' => 'Gagal menghapus kamar'], 500);
        } catch (\Exception $e) {
            Log::error("Destroy kamar error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
