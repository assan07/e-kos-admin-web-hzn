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
        // Log::info("Total Rumah Kos: {$totalKos}");

        $totalKamar = 0;

        foreach ($kosDocuments as $kosDoc) {
            $kosId = basename($kosDoc['name']);
            $kamarDocuments = $firebase->fetchCollection("rumah_kos/$kosId/kamar");
            $jumlahKamar = count($kamarDocuments);
            $totalKamar += $jumlahKamar;
            // Log::info("Kos {$kosId} memiliki {$jumlahKamar} kamar");
        }

        // 2. Fetch Kamar Terisi
        $totalKamarTerisi = 0;
        $pesananDocuments = $firebase->fetchCollection('pesanan');

        foreach ($pesananDocuments as $doc) {
            $status = $doc['fields']['status']['stringValue'] ?? '';
            if ($status === 'diterima') {
                $totalKamarTerisi++;
            }
        }

        // Log::info("Total Kamar Terisi: {$totalKamarTerisi}");

        // 3. Fetch Penghuni
        $penghuniDocuments = $firebase->fetchCollection('pesanan');
        $totalPenghuni = 0;
        foreach ($penghuniDocuments as $doc) {
            $status = $doc['fields']['status']['stringValue'] ?? '';
            if ($status === 'diterima') {
                $totalPenghuni++;
            }
        }
        // Log::info("Total Penghuni: {$totalPenghuni}");
        $penghuniBaru = $penghuniDocuments; // ambil semua, bisa slice nanti

        // 4. Fetch Payments
        $pesananDocuments = $firebase->fetchCollection('pesanan');
        $pesanan = array_map(function ($doc) {
            return [
                'id' => basename($doc['name']),
                'amount' => $doc['fields']['harga']['integerValue'] ?? 0,
                'status' => $doc['fields']['status']['stringValue'] ?? 'diproses',
                'no_hp' => $doc['fields']['no_hp']['stringValue'] ?? '-',
                'created_at' => \Carbon\Carbon::parse($doc['fields']['timestamp']['stringValue'] ?? now())
                    ->format('M d, Y, h:ia'),
                'nama' => $doc['fields']['nama']['stringValue'] ?? 'No Name',
                'kamar' => $doc['fields']['kamar']['stringValue'] ?? '-',
                'kos' => $doc['fields']['kos']['stringValue'] ?? '-',
            ];
        }, array_reverse($pesananDocuments));

        Log::info("Payments fetched: " . count($pesanan));

        //5. fatch foto user
        foreach ($penghuniBaru as &$doc) {
            $uid = $doc['fields']['user_id']['stringValue'] ?? null;
            if ($uid) {
                $userDoc = $firebase->fetchDocument('users', $uid);
                $doc['user_photo'] = $userDoc['fields']['photoUrl']['stringValue'] ?? null;
                            }
            Log::info("User ID: {$uid}, Photo URL: " . ($doc['user_photo'] ?? 'No Photo'));
        }

        return view('admin.dashboard', compact(
            'totalKos',
            'totalKamar',
            'totalKamarTerisi',
            'totalPenghuni',
            'penghuniBaru',
            'pesanan',

        ));
    }
}
