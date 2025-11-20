<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseRestService;
use App\Exports\PaymentsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use DateTime;


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
        try {
            // List bulan default bisa dihapus, pakai input type="month" di Blade
            return view('pembayaran.add_payment');
        } catch (\Exception $e) {
            Log::error("Load Add Payment Form error: " . $e->getMessage());
            return back()->with('error', 'Gagal memuat data form pembayaran.');
        }
    }

    public function addPayment(Request $request)
    {
        try {
            // VALIDASI INPUT
            $request->validate([
                'nama' => 'required|string',
                'kos' => 'required|string',
                'kamar' => 'required|string',
                'bulan' => 'required|string', // akan pakai format YYYY-MM
                'harga' => 'required|integer',
                'bukti_url' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:3000'
            ]);

            $nama = $request->nama;
            $kos = $request->kos;
            $kamar = $request->kamar;
            $bulanInput = $request->bulan; // format "YYYY-MM"

            // Convert input ke DateTime object
            $bulanBaru = DateTime::createFromFormat('Y-m', $bulanInput);
            if (!$bulanBaru) {
                return back()->with('error', 'Format bulan tidak valid.');
            }
            $bulanBaru->setDate($bulanBaru->format('Y'), $bulanBaru->format('m'), 1); // set day=1

            // Ambil semua pembayaran user
            $existing = $this->firebase->fetchCollectionWhere('tagihan', 'nama', $nama);
            if (!is_array($existing)) $existing = [];

            // STEP 2 — Cek jika sudah pernah bayar bulan ini
            foreach ($existing as $doc) {
                if (!is_array($doc) || !isset($doc['fields'])) continue;
                $bulanOldStr = $doc['fields']['bulan']['stringValue'] ?? null;
                if ($bulanOldStr) {
                    $bulanOld = DateTime::createFromFormat('Y-m', $this->firebase->parseBulanToDate($bulanOldStr));
                    if ($bulanOld && $bulanOld->format('Y-m') === $bulanBaru->format('Y-m')) {
                        return back()->with('error', 'Bulan ini sudah dibayar sebelumnya.');
                    }
                }
            }

            // STEP 3 — Tentukan bulan terakhir yang sudah dibayar
            $lastPaidDate = null;
            foreach ($existing as $doc) {
                if (!is_array($doc) || !isset($doc['fields'])) continue;
                $bulanOldStr = $doc['fields']['bulan']['stringValue'] ?? null;
                if ($bulanOldStr) {
                    $dateOld = DateTime::createFromFormat('Y-m', $this->firebase->parseBulanToDate($bulanOldStr));
                    if ($dateOld && (!$lastPaidDate || $dateOld > $lastPaidDate)) {
                        $lastPaidDate = $dateOld;
                    }
                }
            }

            // STEP 4 — Validasi anti-lompatan
            if ($lastPaidDate) {
                $nextMonth = (clone $lastPaidDate)->modify('+1 month');
                if ($bulanBaru->format('Y-m') !== $nextMonth->format('Y-m')) {
                    return back()->with('error', 'Pembayaran harus berurutan. Bayar bulan ' .
                        $nextMonth->format('F Y') . ' terlebih dahulu.');
                }
            }

            // ===== UPLOAD FILE =====
            $buktiUrl = '-';
            if ($request->hasFile('bukti_url')) {
                $file = $request->file('bukti_url');
                $fileName = 'bukti_pembayaran_' . time() . '.' . $file->getClientOriginalExtension();
                $firebasePath = "tagihan/bukti/" . $fileName;

                $upload = $this->firebase->uploadFile($file->getPathname(), $firebasePath);
                if (is_array($upload) && isset($upload['url'])) {
                    $buktiUrl = $upload['url'];
                } elseif (is_string($upload)) {
                    $buktiUrl = $upload;
                }
            }

            // ===== SIMPAN KE FIRESTORE =====
            $data = [
                'nama' => ['stringValue' => $nama],
                'kos' => ['stringValue' => $kos],
                'kamar' => ['stringValue' => $kamar],
                'bulan' => ['stringValue' => $bulanBaru->format('Y-m')],
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

    public function delete($id)
    {
        try {
            // Ambil dokumen berdasarkan id
            $doc = $this->firebase->fetchDocument('tagihan', $id);

            if (!$doc) {
                return back()->with('error', 'Data pembayaran tidak ditemukan.');
            }

            // Hapus dokumen
            $this->firebase->deleteDocument('tagihan', $id);

            return back()->with('success', 'Pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error("Delete payment error: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus pembayaran.');
        }
    }



    // ====================== EXPORT EXCEL ==========================
    public function download($kos)
    {
        try {
            Log::info("Memulai download untuk kos: {$kos}");

            // Buat instance export dengan firebase
            $export = new PaymentsExport($kos, $this->firebase);

            // Ambil koleksi dulu untuk debug
            $collection = $export->collection();
            Log::info("Jumlah data yang diambil: " . $collection->count());

            if ($collection->isEmpty()) {
                Log::warning("Tidak ada data pembayaran ditemukan untuk kos: {$kos}");
                return back()->with('error', "Tidak ada data pembayaran untuk kos: {$kos}");
            }

            // Download Excel
            return Excel::download($export, "pembayaran_{$kos}.xlsx");
        } catch (\Exception $e) {
            Log::error("Download error: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->with('error', 'Gagal download file. Error: ' . $e->getMessage());
        }
    }
}
