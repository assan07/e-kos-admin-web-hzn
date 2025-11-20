<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseRestService;
use Illuminate\Support\Facades\Log;

class PesananController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseRestService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index()
    {
        $pesananCollection = $this->firebase->fetchCollection('pesanan');


        Log::info('Pesanan Collection:', $pesananCollection);

        foreach ($pesananCollection as $doc) {
            // idDoc di Firestore ada di akhir path 'name'
            $pathParts = explode('/', $doc['name']);
            $idDoc = end($pathParts);

            $fields = $doc['fields'] ?? [];

            $pesanan[] = [
                'idDoc'     => $idDoc,
                'nama'      => $fields['nama']['stringValue'] ?? '-',
                'kos'       => $fields['kos']['stringValue'] ?? '-',
                'kamar'     => $fields['kamar']['stringValue'] ?? '-',
                'harga'     => isset($fields['harga']['integerValue']) ? (int)$fields['harga']['integerValue'] : 0,
                'no_hp'     => $fields['no_hp']['stringValue'] ?? '-',
                'status'    => $fields['status']['stringValue'] ?? 'diproses',
                'timestamp' => isset($fields['timestamp']['timestampValue']) ? \Carbon\Carbon::parse($fields['timestamp']['timestampValue'])->format('Y-m-d') : '-',
                'foto_ktp'  => $fields['foto_ktp']['stringValue'] ?? null,
                'user_id'   => $fields['user_id']['stringValue'] ?? null,
            ];
        }

        return view('pesanan.pesan_kamar', compact('pesanan'));
    }

    // Menampilkan detail pesanan
    public function detail($idDoc)
    {
        $doc = $this->firebase->fetchDocument('pesanan', $idDoc);

        // Pastikan field ada
        $pesanan = [
            'idDoc'     => $idDoc, // <--- ini penting
            'nama'      => $doc['fields']['nama']['stringValue'] ?? '-',
            'kos'       => $doc['fields']['kos']['stringValue'] ?? '-',
            'kamar'     => $doc['fields']['kamar']['stringValue'] ?? '-',
            'harga'     => isset($doc['fields']['harga']['integerValue']) ? (int)$doc['fields']['harga']['integerValue'] : 0,
            'no_hp'     => $doc['fields']['no_hp']['stringValue'] ?? '-',
            'status'    => $doc['fields']['status']['stringValue'] ?? 'diproses',
            'timestamp' => isset($doc['fields']['timestamp']['timestampValue'])
                ? \Carbon\Carbon::parse($doc['fields']['timestamp']['timestampValue'])->format('Y-m-d')
                : null,
            'foto_ktp'  => $doc['fields']['foto_ktp']['stringValue'] ?? null,
            'user_id'   => $doc['fields']['user_id']['stringValue'] ?? null,
        ];

        return view('pesanan.update_pesan_kamar', compact('pesanan'));
    }

    // Method untuk update status pesanan
    public function update(Request $request, $idDoc)
    {
        $request->validate([
            'status' => 'required|in:diproses,diterima,ditolak',
        ]);

        $status = $request->input('status');

        // Ambil dokumen lama
        $oldDoc = $this->firebase->fetchDocument('pesanan', $idDoc);
        $fields = $oldDoc['fields'] ?? [];

        // Merge status baru
        $updateData = [
            'nama' => $fields['nama']['stringValue'] ?? '-',
            'kos' => $fields['kos']['stringValue'] ?? '-',
            'kamar' => $fields['kamar']['stringValue'] ?? '-',
            'harga' => isset($fields['harga']['integerValue']) ? (int)$fields['harga']['integerValue'] : 0,
            'no_hp' => $fields['no_hp']['stringValue'] ?? '-',
            'timestamp' => $fields['timestamp']['timestampValue'] ?? null,
            'status' => $status,
            'foto_ktp' => $fields['foto_ktp']['stringValue'] ?? null,
            'user_id' => $fields['user_id']['stringValue'] ?? null,
        ];

        try {
            $this->firebase->updateDocument('pesanan', $idDoc, $updateData);
            return redirect()->route('admin.pesanan.detail', $idDoc)
                ->with('success', 'Status pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('admin.pesanan.detail', $idDoc)
                ->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }


    // Hapus pesanan
    public function delete($idDoc)
    {
        $this->firebase->deleteDocument('pesanan', $idDoc);
        return redirect()->route('admin.pesanan.index')->with('success', 'Pesanan berhasil dihapus');
    }
}
