<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    /**
     * Create document Firestore
     */
    public function createDocument($collection, $fields)
    {
        $token = $this->getAccessToken();
        if (!$token) throw new \Exception("Gagal generate access token");

        $url = "{$this->baseUrl}/{$collection}";

        $formattedFields = [];
        foreach ($fields as $key => $value) {
            if (is_int($value)) {
                $formattedFields[$key] = ['integerValue' => (string)$value];
            } else {
                $formattedFields[$key] = ['stringValue' => $value];
            }
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer $token",
            'Content-Type'  => 'application/json'
        ])->post($url, ['fields' => $formattedFields]);

        if (!$response->successful()) {
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
}
