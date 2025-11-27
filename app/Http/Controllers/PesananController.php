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
        try {
            $collection = $this->firebase->fetchCollection('pesanan');
            if (!is_array($collection)) $collection = [];
        } catch (\Exception $e) {
            $collection = [];
        }

        $pesanan = [];

        foreach ($collection as $doc) {
            $pathParts = explode('/', $doc['name']);
            $idDoc = end($pathParts);

            $fields = $doc['fields'] ?? [];

            $pesanan[] = [
                'idDoc'     => $idDoc,
                'nama'      => $fields['nama']['stringValue'] ?? '-',
                'kos'       => $fields['kos']['stringValue'] ?? '-',
                'kamar'     => $fields['kamar']['stringValue'] ?? '-',
                'harga'     => isset($fields['harga']['integerValue'])
                    ? (int)$fields['harga']['integerValue']
                    : 0,
                'no_hp'     => $fields['no_hp']['stringValue'] ?? '-',
                'status'    => $fields['status']['stringValue'] ?? 'diproses',
                'timestamp' => isset($fields['timestamp']['timestampValue'])
                    ? \Carbon\Carbon::parse($fields['timestamp']['timestampValue'])->format('Y-m-d')
                    : '-',
                'foto_ktp'  => $fields['foto_ktp']['stringValue'] ?? null,
                'user_id'   => $fields['user_id']['stringValue'] ?? null,
            ];
        }

        // card summary counts
        $totalPesanan = count($pesanan);

        $totalDiproses = count(array_filter($pesanan, fn($p) => $p['status'] === 'diproses'));
        $totalDiterima = count(array_filter($pesanan, fn($p) => $p['status'] === 'diterima'));
        $totalDitolak  = count(array_filter($pesanan, fn($p) => $p['status'] === 'ditolak'));

        return view('pesanan.pesan_kamar', compact(
            'pesanan',
            'totalPesanan',
            'totalDiproses',
            'totalDiterima',
            'totalDitolak'
        ));
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

    public function update(Request $request, $idDoc)
    {
        // Validasi status
        $request->validate([
            'status_pembayaran' => 'required|in:belum_bayar,menunggu,diterima,ditolak',
        ]);

        try {
            // Ambil data dokumen lama
            $oldDoc = $this->firebase->fetchDocument('tagihan', $idDoc);
            if (!$oldDoc) {
                return back()->with('error', 'Data pembayaran tidak ditemukan.');
            }

            $fields = $oldDoc['fields'] ?? [];

            // Build ulang data lengkap (full merge)
            $updateData = [
                'nama' => $fields['nama']['stringValue'] ?? '-',
                'kos' => $fields['kos']['stringValue'] ?? '-',
                'kamar' => $fields['kamar']['stringValue'] ?? '-',
                'bulan' => $fields['bulan']['stringValue'] ?? '-',
                'harga' => isset($fields['harga']['integerValue'])
                    ? (int)$fields['harga']['integerValue']
                    : 0,

                // FIELD YANG DIUBAH
                'status_pembayaran' => $request->status_pembayaran,

                // Field opsional lainnya
                'bukti_url' => $fields['bukti_url']['stringValue'] ?? null,
                'user_id' => $fields['user_id']['stringValue'] ?? null,
                'timestamp' => $fields['timestamp']['timestampValue'] ?? null,
            ];

            // Kirim update ke Firestore
            $this->firebase->updateDocument('tagihan', $idDoc, $updateData);

            return redirect()
                ->route('admin.pembayaran.detail', $idDoc)
                ->with('success', 'Data pembayaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui pembayaran: ' . $e->getMessage());
        }
    }


    // Hapus pesanan
    public function delete($idDoc)
    {
        $this->firebase->deleteDocument('pesanan', $idDoc);
        return redirect()->route('admin.pesanan.index')->with('success', 'Pesanan berhasil dihapus');
    }
}
