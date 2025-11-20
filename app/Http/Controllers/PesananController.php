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

        $pesanan = [];

        Log::info('Pesanan Collection:', $pesananCollection);

        foreach ($pesananCollection as $idDoc => $doc) {

            // Extract value dari Firestore raw format
            $fields = $doc['fields'] ?? [];

            $pesanan[] = [
                'idDoc'     => $idDoc,
                'nama'      => $fields['nama']['stringValue'] ?? '-',
                'kos'       => $fields['kos']['stringValue'] ?? '-',
                'kamar'     => $fields['kamar']['stringValue'] ?? '-',
                'harga'     => isset($fields['harga']['integerValue']) ? (int)$fields['harga']['integerValue'] : 0,
                'no_hp'     => $fields['no_hp']['stringValue'] ?? '-',
                'status'    => $fields['status']['stringValue'] ?? 'diproses',
                'timestamp' => isset($fields['timestamp']['timestampValue']) ? \Carbon\Carbon::parse($fields['timestamp']['timestampValue'])->format('Y-m-d'): '-',

                'foto_ktp'  => $fields['foto_ktp']['stringValue'] ?? null,
                'user_id'   => $fields['user_id']['stringValue'] ?? null,
            ];
        }

        return view('pesanan.pesan_kamar', compact('pesanan'));
    }

    // Menampilkan detail pesanan
    public function detail($idDoc)
    {
        $pesanan = $this->firebase->fetchDocument('pesanan', $idDoc);

        // Pastikan field ada, untuk mempermudah di Blade
        $pesanan = [
            'idDoc'     => $idDoc,
            'nama'      => $pesanan['nama'] ?? '-',
            'kos'       => $pesanan['kos'] ?? '-',
            'kamar'     => $pesanan['kamar'] ?? '-',
            'harga'     => $pesanan['harga'] ?? 0,
            'no_hp'     => $pesanan['no_hp'] ?? '-',
            'status'    => $pesanan['status'] ?? 'diproses',
            'timestamp' => $pesanan['timestamp'] ?? '-',
            'foto_ktp'  => $pesanan['foto_ktp'] ?? null,
            'user_id'   => $pesanan['user_id'] ?? null,
        ];

        return view('pesanan.detail_pesanan', compact('pesanan'));
    }

    // Hapus pesanan
    public function delete($idDoc)
    {
        $this->firebase->deleteDocument('pesanan', $idDoc);
        return redirect()->route('admin.pesanan.index')->with('success', 'Pesanan berhasil dihapus');
    }
}
