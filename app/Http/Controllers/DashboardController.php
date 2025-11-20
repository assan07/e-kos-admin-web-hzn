<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseRestService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(FirebaseRestService $firebase)
    {
        // 1. Fetch Rumah Kos
        $kosDocuments = $firebase->fetchCollection('rumah_kos');
        $totalKos = count($kosDocuments);
        Log::info("Total Rumah Kos: {$totalKos}");

        $totalKamar = 0;
        $totalKamarTerisi = 0;

        foreach ($kosDocuments as $kosDoc) {
            $kosId = basename($kosDoc['name']);
            $kamarDocuments = $firebase->fetchCollection("rumah_kos/$kosId/kamar");
            $jumlahKamar = count($kamarDocuments);
            $totalKamar += $jumlahKamar;
            Log::info("Kos {$kosId} memiliki {$jumlahKamar} kamar");

            $terisi = 0;
            foreach ($kamarDocuments as $kamar) {
                if (($kamar['fields']['status']['stringValue'] ?? '') === 'terisi') {
                    $totalKamarTerisi++;
                    $terisi++;
                }
            }
            Log::info("Kos {$kosId} memiliki {$terisi} kamar terisi");
        }

        // 2. Fetch Penghuni
        $penghuniDocuments = $firebase->fetchCollection('users');
        $totalPenghuni = count($penghuniDocuments);
        Log::info("Total Penghuni: {$totalPenghuni}");
        $penghuniBaru = $penghuniDocuments; // ambil semua, bisa slice nanti

        // 3. Fetch Payments
        $pesananDocuments = $firebase->fetchCollection('pesanan');
        $pesanan = array_map(function ($doc) {
            return [
                'id' => basename($doc['name']),
                'amount' => $doc['fields']['harga']['integerValue'] ?? 0,
                'status' => $doc['fields']['status']['stringValue'] ?? 'Pending',
                'created_at' => \Carbon\Carbon::parse($doc['fields']['timestamp']['stringValue'] ?? now())
                    ->format('M d, Y, h:ia'),
                'nama' => $doc['fields']['nama']['stringValue'] ?? 'No Name',
                'kamar' => $doc['fields']['kamar']['stringValue'] ?? '-',
                'kos' => $doc['fields']['kos']['stringValue'] ?? '-',
            ];
        }, array_reverse($pesananDocuments));

        Log::info("Payments fetched: " . count($pesanan));

        // 4. Optional: langsung dump kalau mau lihat di browser
        // dd($totalKos, $totalKamar, $totalKamarTerisi, $totalPenghuni, $penghuniBaru, $payments);

        return view('admin.dashboard', compact(
            'totalKos',
            'totalKamar',
            'totalKamarTerisi',
            'totalPenghuni',
            'penghuniBaru',
            'pesanan'
        ));
    }
}
