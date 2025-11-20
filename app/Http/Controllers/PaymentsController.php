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
    // ====================== DETAIL ==========================

    public function detail($idDoc)
    {
        try {
            $doc = $this->firebase->fetchDocument('tagihan', $idDoc);

            if (!$doc) {
                return back()->with('error', 'Data pembayaran tidak ditemukan.');
            }

            $fields = $doc['fields'] ?? [];

            $pembayaran = [
                'id_doc' => $idDoc,
                'nama' => $fields['nama']['stringValue'] ?? '-',
                'kos' => $fields['kos']['stringValue'] ?? '-',
                'kamar' => $fields['kamar']['stringValue'] ?? '-',
                'bulan' => $fields['bulan']['stringValue'] ?? '-',
                'harga' => isset($fields['harga']['integerValue'])
                    ? (int)$fields['harga']['integerValue']
                    : 0,
                'status_pembayaran' => $fields['status_pembayaran']['stringValue'] ?? 'belum_bayar',
                'bukti_url' => $fields['bukti_url']['stringValue'] ?? null,
            ];

            return view('pembayaran.update_pembayaran', compact('pembayaran'));
        } catch (\Exception $e) {
            Log::error("Payment detail error: " . $e->getMessage());
            return back()->with('error', 'Gagal mengambil detail pembayaran.');
        }
    }


    public function update(Request $request, $idDoc)
    {
        $request->validate([
            'status_pembayaran' => 'required|in:sudah_bayar,belum_bayar,ditolak',
        ]);

        try {
            // Ambil data lama dari koleksi TAGIHAN
            $oldDoc = $this->firebase->fetchDocument('tagihan', $idDoc);

            if (!$oldDoc || !isset($oldDoc['fields'])) {
                return back()->with('error', 'Dokumen tidak ditemukan.');
            }

            // Convert FirestoreValue ke raw value biasa
            $raw = [];
            foreach ($oldDoc['fields'] as $key => $val) {
                if (isset($val['stringValue'])) $raw[$key] = $val['stringValue'];
                elseif (isset($val['integerValue'])) $raw[$key] = (int)$val['integerValue'];
                elseif (isset($val['timestampValue'])) $raw[$key] = $val['timestampValue'];
                else $raw[$key] = null;
            }

            // Ganti status_pembayaran
            $raw['status_pembayaran'] = $request->status_pembayaran;

            // Convert kembali jadi FirestoreValue
            $updateData = $this->firebase->formatFields($raw);

            // Update Firestore
            $this->firebase->updateDocument('tagihan', $idDoc, $updateData);

            return redirect()
                ->route('admin.pembayaran.detail', $idDoc)
                ->with('success', 'Status pembayaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    public function addPaymentForm()
    {
        return view('pembayaran.add_payment');
    }

    // ====================== ADD PAYMENT MANUAL ==========================
    public function addPayment(Request $request)
    {
        try {
            // VALIDASI INPUT
            $request->validate([
                'nama' => 'required|string',
                'kos' => 'required|string',
                'kamar' => 'required|string',
                'bulan' => 'required|string',
                'harga' => 'required|integer',
                'bukti_url' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:3000'
            ]);

            $nama = $request->nama;
            $bulanBaru = $request->bulan;

            // STEP 1 — Ambil semua pembayaran user
            $existing = $this->firebase->fetchCollectionWhere(
                'tagihan',
                'nama',
                $nama
            );

            // STEP 2 — Cek jika sudah pernah bayar bulan ini
            foreach ($existing as $doc) {
                if (($doc['fields']['bulan']['stringValue'] ?? '') === $bulanBaru) {
                    return back()->with('error', 'Bulan ini sudah dibayar sebelumnya.');
                }
            }

            // Convert bulan baru ke date
            $bulanBaruDate = $this->firebase->parseBulanToDate($bulanBaru);
            if (!$bulanBaruDate) {
                return back()->with('error', 'Format bulan tidak valid.');
            }

            // STEP 3 — Tentukan bulan terakhir yang sudah dibayar
            $lastPaidDate = null;

            foreach ($existing as $doc) {
                $bulanOld = $doc['fields']['bulan']['stringValue'] ?? null;
                $dateOld = $this->firebase->parseBulanToDate($bulanOld);

                if ($dateOld && (!$lastPaidDate || $dateOld > $lastPaidDate)) {
                    $lastPaidDate = $dateOld;
                }
            }

            // STEP 4 — Validasi aturan anti-lompatan
            if ($lastPaidDate) {

                // a) Tidak boleh bayar bulan sebelum pembayaran pertama
                if ($bulanBaruDate < $lastPaidDate) {
                    return back()->with('error', 'Tidak boleh membayar bulan sebelum pembayaran terakhir.');
                }

                // b) Tidak boleh skip (harus bulan selanjutnya tepat)
                $nextMonth = date("Y-m-01", strtotime("+1 month", strtotime($lastPaidDate)));

                if ($bulanBaruDate !== $nextMonth) {
                    return back()->with('error', 'Pembayaran harus berurutan. Bayar bulan ' .
                        date("F Y", strtotime($nextMonth)) . ' terlebih dahulu.');
                }
            }

            // ===== LANJUT UPLOAD FILE & SIMPAN =====

            $buktiUrl = '-';

            if ($request->hasFile('bukti_url')) {
                $file = $request->file('bukti_url');
                $fileName = 'bukti_pembayaran_' . time() . '.' . $file->getClientOriginalExtension();

                $firebasePath = "tagihan/bukti/" . $fileName;
                $upload = $this->firebase->uploadFile($firebasePath, $file->getPathname());

                if ($upload['success']) {
                    $buktiUrl = $upload['url'];
                }
            }

            $data = [
                'nama' => ['stringValue' => $nama],
                'kos' => ['stringValue' => $request->kos],
                'kamar' => ['stringValue' => $request->kamar],
                'bulan' => ['stringValue' => $bulanBaru],
                'harga' => ['integerValue' => $request->harga],
                'status_pembayaran' => ['stringValue' => 'sudah_bayar'],
                'bukti_url' => ['stringValue' => $buktiUrl],
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
