<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\File;
use illuminate\Support\Str;


class FirebaseRestService
{
    protected $projectId;
    protected $bucket;
    protected $serviceAccount;
    protected $baseUrl;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID');
        $this->bucket    = env('FIREBASE_STORAGE_BUCKET'); // tanpa gs://
        $this->serviceAccount = json_decode(file_get_contents(storage_path('app/firebase/service-account.json')), true);

        // Base URL Firestore
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
    }

    /**
     * Generate access token from service account
     */
    protected function getAccessToken()
    {
        $now = time();
        $jwtHeader = ['alg' => 'RS256', 'typ' => 'JWT'];
        $jwtClaim = [
            'iss' => $this->serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/datastore https://www.googleapis.com/auth/devstorage.full_control',
            'aud' => $this->serviceAccount['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $base64UrlEncode = function ($data) {
            return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
        };

        $header = $base64UrlEncode($jwtHeader);
        $claim  = $base64UrlEncode($jwtClaim);
        $signingInput = $header . '.' . $claim;

        $privateKey = $this->serviceAccount['private_key'];
        openssl_sign($signingInput, $signature, $privateKey, "SHA256");

        $jwt = $signingInput . '.' . rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        $response = Http::asForm()->post($this->serviceAccount['token_uri'], [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        return $response->json()['access_token'] ?? null;
    }
    // ====================================Rumah Kos ====================================
    /**
     * Generate custom ID kos berformat RK_XXX
     * $lastNumber = angka terakhir dari ID sebelumnya
     */

    /**
     * Generate Firestore document ID
     */
    public static function generateIdDoc()
    {
        // Bisa disesuaikan formatnya
        return Str::upper('DOC_' . Str::random(12));
    }

    /**
     * Upload file ke Firebase Storage
     */
    public function uploadFile($localPath, $remotePath)
    {
        $token = $this->getAccessToken();
        if (!$token) throw new \Exception("Gagal generate access token");

        $url = "https://storage.googleapis.com/upload/storage/v1/b/{$this->bucket}/o?uploadType=media&name={$remotePath}";

        $response = Http::withHeaders([
            'Authorization' => "Bearer $token",
            'Content-Type'  => mime_content_type($localPath)
        ])->withBody(file_get_contents($localPath), mime_content_type($localPath))
            ->post($url);

        if (!$response->successful()) {
            throw new \Exception("Gagal upload file: " . $response->body());
        }

        // Public URL
        return "https://firebasestorage.googleapis.com/v0/b/{$this->bucket}/o/" . rawurlencode($remotePath) . "?alt=media";
    }

    // =================================== Firestore Helpers ====================================
    /** time stap */
    function fsTimestamp($carbon)
    {
        return ['timestampValue' => $carbon->toISOString()];
    }


    /**
     * Create document Firestore
     */


    public function createDocument(string $collectionPath, array $fieldsFormatted, string $docId = null)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            throw new \Exception("Gagal generate access token");
        }
        // Force formatting
        $formatted = $this->formatFields($fieldsFormatted);

        $url = $docId
            ? "{$this->baseUrl}/{$collectionPath}?documentId={$docId}"
            : "{$this->baseUrl}/{$collectionPath}";

        $payload = ['fields' => $formatted];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        if ($response->failed()) {
            Log::error("createDocument failed: " . $response->body());
            throw new \Exception("Gagal simpan Firestore: " . $response->body());
        }

        return $response->json();
    }



    /**
     * Fetch seluruh dokumen collection
     */
    public function fetchCollection($collectionName)
    {
        $token = $this->getAccessToken();
        if (!$token) throw new \Exception("Gagal generate access token");

        $url = "{$this->baseUrl}/{$collectionName}";

        $response = Http::withHeaders([
            'Authorization' => "Bearer $token",
            'Content-Type' => 'application/json'
        ])->get($url);

        if ($response->failed()) {
            Log::error("FetchCollection Response: " . $response->body());
            throw new \Exception("Gagal fetch collection: {$collectionName}");
        }

        $data = $response->json();
        return $data['documents'] ?? [];
    }

    /**
     * Fetch subcollection dokumen tertentu
     */
    public function fetchSubcollection($parentId, $subCollection)
    {
        $token = $this->getAccessToken();
        if (!$token) throw new \Exception("Gagal generate access token");

        $url = "{$this->baseUrl}/rumah_kos/{$parentId}/{$subCollection}";

        $response = Http::withHeaders([
            'Authorization' => "Bearer $token",
            'Content-Type' => 'application/json'
        ])->get($url);

        if ($response->failed()) {
            Log::error("FetchSubcollection Response: " . $response->body());
            throw new \Exception("Gagal fetch subcollection: {$subCollection} dari dokumen {$parentId}");
        }

        $data = $response->json();
        return $data['documents'] ?? [];
    }
    /**
     * Fetch single document
     */
    public function fetchDocument($collection, $docId)
    {
        $token = $this->getAccessToken();
        if (!$token) throw new \Exception("Gagal generate access token");

        $url = "{$this->baseUrl}/{$collection}/{$docId}";

        $response = Http::withHeaders([
            'Authorization' => "Bearer $token",
            'Content-Type' => 'application/json'
        ])->get($url);

        if ($response->failed()) {
            throw new \Exception("Dokumen {$docId} di collection {$collection} tidak ditemukan: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Fetch nested document (subcollection document page kamar)
     */
    public function fetchNestedDocument($collection1, $docId1, $collection2, $docId2)
    {
        $token = $this->getAccessToken();
        if (!$token) throw new \Exception("Gagal generate access token");

        $url = "{$this->baseUrl}/{$collection1}/{$docId1}/{$collection2}/{$docId2}";

        $response = Http::withHeaders([
            'Authorization' => "Bearer $token",
            'Content-Type' => 'application/json'
        ])->get($url);

        if ($response->failed()) {
            Log::error("FetchNestedDocument FAILED", [
                'url' => $url,
                'body' => $response->body()
            ]);
            return null;
        }

        return $response->json();
    }


    // ==================================== Kamar ====================================
    /**
     * Upload image kamar ke Firebase Storage
     */
    public function uploadImage($file, $folder = 'kamar')
    {
        try {
            $fileName = $folder . '/' . time() . '_' . $file->getClientOriginalName();
            $url = "https://firebasestorage.googleapis.com/v0/b/{$this->bucket}/o?uploadType=media&name={$fileName}";

            $response = Http::withHeaders([
                'Content-Type' => $file->getMimeType(),
            ])->post($url, file_get_contents($file));

            if (!$response->successful()) {
                throw new \Exception('Upload gagal: ' . $response->body());
            }

            return "https://firebasestorage.googleapis.com/v0/b/{$this->bucket}/o/" .
                urlencode($fileName) . "?alt=media";
        } catch (\Exception $e) {
            Log::error("Firebase uploadImage error: " . $e->getMessage());
            return null;
        }
    }


    public function fetchDocumentById($collection, $documentId)
    {
        try {
            $url = "{$this->baseUrl}/projects/{$this->projectId}/databases/(default)/documents/{$collection}/{$documentId}";
            $response = Http::get($url);

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error("fetchDocumentById error: " . $e->getMessage());
            return null;
        }
    }
    public function updateDocument($collection, $documentId, array $data)
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) throw new \Exception("Gagal generate access token");

            // URL: gunakan baseUrl + collection relatif + documentId
            $url = "{$this->baseUrl}/{$collection}/{$documentId}";

            $payload = [
                'fields' => $this->formatFields($data)
            ];

            Log::info("DEBUG UPDATE DOCUMENT REQUEST", [
                'url' => $url,
                'payload' => $payload
            ]);

            $response = Http::withHeaders([
                'Authorization' => "Bearer $token",
                'Content-Type' => 'application/json',
            ])->patch($url, $payload);

            Log::info("DEBUG UPDATE DOCUMENT RESPONSE", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->failed()) {
                throw new \Exception("Update gagal: " . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error("updateDocument error: " . $e->getMessage());
            return false;
        }
    }



    public function deleteDocument($collection, $documentId)
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) throw new \Exception("Gagal generate access token");

            $url = "{$this->baseUrl}/{$collection}/{$documentId}";

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json'
            ])->delete($url);

            Log::info("DEBUG deleteDocument", [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("deleteDocument error: " . $e->getMessage());
            return false;
        }
    }


    public function formatFields(array $data)
    {
        $fields = [];

        foreach ($data as $key => $value) {

            // Kalau sudah berbentuk FirestoreValue → biarkan.
            if (is_array($value) && $this->isFirestoreValue($value)) {
                $fields[$key] = $value;
                continue;
            }

            // INTEGER
            if (is_int($value)) {
                $fields[$key] = ['integerValue' => (string)$value];
                continue;
            }

            // ARRAY → arrayValue
            if (is_array($value)) {

                $fields[$key] = [
                    'arrayValue' => [
                        'values' => array_map(function ($item) {

                            // kalau item sudah FirestoreValue → jangan diganggu
                            if (is_array($item) && $this->isFirestoreValue($item)) {
                                return $item;
                            }

                            // integer
                            if (is_int($item)) {
                                return ['integerValue' => (string)$item];
                            }

                            // default: stringValue
                            return ['stringValue' => (string)$item];
                        }, $value)
                    ]
                ];

                continue;
            }
            // TIMESTAMP (Carbon instance)
            if ($value instanceof \Carbon\Carbon) {
                $fields[$key] = [
                    'timestampValue' => $value->toRfc3339String()
                ];
                continue;
            }


            // DEFAULT: string
            $fields[$key] = ['stringValue' => (string)$value];
        }

        return $fields;
    }


    private function isFirestoreValue($value)
    {
        if (!is_array($value)) return false;

        $allowed = [
            'stringValue',
            'integerValue',
            'booleanValue',
            'timestampValue',
            'arrayValue',
            'mapValue',
            'nullValue'
        ];

        return count(array_intersect(array_keys($value), $allowed)) > 0;
    }

    function buildFirestoreString($value)
    {
        return [
            'stringValue' => $value ?? ''
        ];
    }
    function buildFirestoreInt($value)
    {
        return [
            'integerValue' => (string)($value ?? 0)
        ];
    }
    function buildFirestoreArray(array $items)
    {
        return [
            'arrayValue' => [
                'values' => array_map(
                    fn($v) => ['stringValue' => (string) $v],
                    $items
                )
            ]
        ];
    }
    function buildFirestoreTimestamp($carbon)
    {
        return [
            'timestampValue' => $carbon->toRfc3339String()
        ];
    }

    // =================================== Mapping Kamar Fields ====================================
    public function mapKamarFieldsToDto(string $idKamar, array $fields, ?string $alamatKos = null): object
    {
        // parse fasilitas
        $fasilitasArr = [];
        if (!empty($fields['fasilitas'])) {
            if (isset($fields['fasilitas']['arrayValue']['values'])) {
                foreach ($fields['fasilitas']['arrayValue']['values'] as $v) {
                    if (isset($v['stringValue'])) $fasilitasArr[] = $v['stringValue'];
                }
            } else {
                $raw = $fields['fasilitas']['stringValue'] ?? $fields['fasilitas'];
                if (is_string($raw)) {
                    $tmp = array_map('trim', explode(',', $raw));
                    $fasilitasArr = array_values(array_filter($tmp, fn($x) => $x !== ''));
                }
            }
        }

        // helper string / int
        $getString = fn($key) => $fields[$key]['stringValue'] ?? ($fields[$key] ?? null);
        $getInt = fn($key) => isset($fields[$key]['integerValue']) ? (int)$fields[$key]['integerValue'] : (int) ($fields[$key]['stringValue'] ?? 0);

        $dto = (object) [
            'id_kamar'     => $idKamar,
            'id'           => $idKamar,
            'nama_kamar'   => $getString('nama_kamar') ?? '',
            'alamat_kamar' => $getString('alamat') ?? $getString('alamat_kamar') ?? $alamatKos ?? '',
            'no_kamar'     => $getString('no_kamar') ?? '',
            'harga_sewa'   => $getInt('harga') ?? $getInt('harga_sewa'),
            'status'       => $getString('status') ?? 'tersedia',
            'fasilitas'    => implode(', ', $fasilitasArr),
            'foto_url'     => $getString('foto') ?? $getString('foto_url') ?? null,
        ];

        return $dto;
    }

    // =================================== Mapping Pembayaran Fields ====================================   
    public function fetchCollectionWhere(string $collection, string $field, $value): array
    {
        $url = "{$this->baseUrl}/projects/{$this->projectId}/databases/(default)/documents/{$collection}?where=" . urlencode(json_encode([
            'fieldFilter' => [
                'field' => ['fieldPath' => $field],
                'op' => 'EQUAL',
                'value' => ['stringValue' => $value],
            ]
        ]));

        $response = Http::withToken($this->accessToken)
            ->get($url);

        if (!$response->successful()) {
            Log::error("Firestore where fetch failed: " . $response->body());
            return [];
        }

        $json = $response->json();
        $documents = $json['documents'] ?? [];

        $result = [];

        foreach ($documents as $doc) {
            $result[] = $this->mapFirestoreDoc($doc);
        }

        return $result;
    }

    // periode parsing
    public function parseBulanToDate($bulan)
    {
        $map = [
            'januari' => 1,
            'februari' => 2,
            'maret' => 3,
            'april' => 4,
            'mei' => 5,
            'juni' => 6,
            'juli' => 7,
            'agustus' => 8,
            'september' => 9,
            'oktober' => 10,
            'november' => 11,
            'desember' => 12,
        ];

        $parts = explode(' ', strtolower($bulan));

        if (count($parts) !== 2) return null;

        $monthName = $parts[0];
        $year = (int)$parts[1];

        if (!isset($map[$monthName])) return null;

        $month = $map[$monthName];

        // Format: 2025-11-01
        return sprintf("%04d-%02d-01", $year, $month);
    }
}
