<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Services\FirebaseRestService;

class PaymentsExport implements FromCollection, WithHeadings
{
    protected $kos;
    protected $firebase;

    public function __construct($kos, FirebaseRestService $firebase)
    {
        $this->kos = $kos;
        $this->firebase = $firebase;
    }

    public function collection()
    {
        $documents = $this->firebase->fetchCollection('tagihan');
        $filtered = [];

        foreach ($documents as $doc) {
            $fields = $doc['fields'] ?? [];
            $kosName = $fields['kos']['stringValue'] ?? '-';

            if ($kosName !== $this->kos) continue;

            $filtered[] = [
                'Nama' => $fields['nama']['stringValue'] ?? '-',
                'Kamar' => $fields['kamar']['stringValue'] ?? '-',
                'Bulan' => $fields['bulan']['stringValue'] ?? '-',
                'Harga' => isset($fields['harga']['integerValue']) ? (int)$fields['harga']['integerValue'] : 0,
                'Status' => $fields['status_pembayaran']['stringValue'] ?? 'belum_bayar',
                'Bukti' => $fields['bukti_url']['stringValue'] ?? '-'
            ];
        }

        return collect($filtered);
    }

    public function headings(): array
    {
        return ['Nama', 'Kamar', 'Bulan', 'Harga', 'Status', 'Bukti'];
    }
}
