<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseRestService;
use App\Exports\PaymentsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;


class PaymentsController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseRestService $firebase)
    {
        $this->firebase = $firebase;
    }

    // ====================== INDEX ==========================
    public function index()
    {
        try {
            $documents = $this->firebase->fetchCollection('tagihan');
            Log::info('Payments Collection:', $documents);
            $allPayments = [];
            $allKos = [];

            foreach ($documents as $doc) {

                // Ambil ID dokumen dengan benar
                $pathParts = explode('/', $doc['name']);
                $idDoc = end($pathParts);

                $fields = $doc['fields'] ?? [];

                $kosName = $fields['kos']['stringValue'] ?? '-';
                $kamarName = $fields['kamar']['stringValue'] ?? '-';

                // ============================
                //  PERBAIKAN UTAMA DI SINI
                // ============================
                $payment = [
                    'id_pesanan' => $idDoc,
                    'nama' => $fields['nama']['stringValue'] ?? '-',
                    'kamar' => $kamarName,
                    'kos' => $kosName,
                    'bulan' => $fields['bulan']['stringValue'] ?? '-',
                    'harga' => isset($fields['harga']['integerValue'])
                        ? (int)$fields['harga']['integerValue']
                        : 0,
                    'status_pembayaran' => $fields['status_pembayaran']['stringValue'] ?? 'belum_bayar',
                    'bukti_url' => $fields['bukti_url']['stringValue'] ?? null,
                ];

                $allPayments[] = $payment;

                // Tambahkan list kos unik
                if (!in_array($kosName, $allKos)) {
                    $allKos[] = $kosName;
                }
            }

            // -----------------
            // Summary (FIXED)
            // -----------------
            $jumlah_bayar = count(array_filter(
                $allPayments,
                fn($p) =>
                $p['status_pembayaran'] === 'sudah_bayar'
            ));

            $jumlah_belum = count(array_filter(
                $allPayments,
                fn($p) =>
                $p['status_pembayaran'] === 'belum_bayar'
            ));

            $jumlah_ditolak = count(array_filter(
                $allPayments,
                fn($p) =>
                $p['status_pembayaran'] === 'ditolak'
            ));

            // Total pemasukan hanya dari yang sudah bayar
            $total_pemasukan = array_sum(
                array_map(fn($p) => $p['status_pembayaran'] === 'sudah_bayar' ? $p['harga'] : 0, $allPayments)
            );

            return view('pembayaran.data_pembayaran', [
                'payments' => $allPayments,
                'allKos' => $allKos,
                'jumlah_bayar' => $jumlah_bayar,
                'jumlah_belum' => $jumlah_belum,
                'jumlah_ditolak' => $jumlah_ditolak,
                'total_pemasukan' => $total_pemasukan,
            ]);
        } catch (\Exception $e) {
            Log::error("Fetch payments error: " . $e->getMessage());
            return back()->with('error', 'Gagal mengambil data pembayaran.');
        }
    }



    // ====================== UPDATE STATUS ==========================
    public function updateStatus(Request $request, $id)
    {
        try {
            $status = $request->status;

            $this->firebase->updateDocument(
                "tagihan",        // nama koleksi
                $id,              // id dokumen
                [
                    'status_pembayaran' => $status
                ]
            );

            return back()->with('success', 'Status pembayaran berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error("Update status error: " . $e->getMessage());
            return back()->with('error', 'Gagal update status pembayaran.');
        }
    }


    // ====================== ADD PAYMENT MANUAL ==========================
    public function addPayment(Request $request)
    {
        try {
            $data = [
                'nama' => ['stringValue' => $request->nama],
                'kos' => ['stringValue' => $request->kos],
                'kamar' => ['stringValue' => $request->kamar],
                'bulan' => ['stringValue' => $request->bulan],
                'harga' => ['integerValue' => $request->harga],
                'status_pembayaran' => ['stringValue' => 'sudah_bayar'],
                'bukti_url' => ['stringValue' => $request->bukti_url ?? '-'],
            ];

            $this->firebase->createDocument('tagihan', $data);

            return back()->with('success', 'Pembayaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error("Add payment error: " . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan pembayaran.');
        }
    }

    // ====================== EXPORT EXCEL ==========================
    public function download($kos)
    {
        try {
            return Excel::download(new PaymentsExport($kos, $this->firebase), "pembayaran_{$kos}.xlsx");
        } catch (\Exception $e) {
            Log::error("Download error: " . $e->getMessage());
            return back()->with('error', 'Gagal download file.');
        }
    }
}
