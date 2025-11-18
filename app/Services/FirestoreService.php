<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirestoreService
{
    protected $projectId;
    protected $token;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID');
        $this->token = $this->generateToken();
    }

    private function generateToken()
    {
        try {
            $scopes = ['https://www.googleapis.com/auth/datastore'];
            $path = base_path(env('FIREBASE_SERVICE_ACCOUNT'));

            if (!file_exists($path)) {
                throw new \Exception("Service Account JSON Not Found");
            }

            $creds = new ServiceAccountCredentials($scopes, $path);
            $token = $creds->fetchAuthToken();
            return $token['access_token'] ?? null;

        } catch (\Exception $e) {
            Log::error("🔥 Firestore Token Error", [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
    private function mapDocuments($documents)
{
    $result = [];

    foreach ($documents as $doc) {
        $id = basename($doc['name']); // Extract ID from path
        $fields = $doc['fields'] ?? [];
        $mapped = ['id' => $id];

        foreach ($fields as $key => $value) {
            $mapped[$key] = reset($value); // take stringValue/integerValue automatically
        }

        $result[] = $mapped;
    }

    return $result;
}


    public function request($method, $path, $data = null)
    {
        try {
            if (!$this->token) {
                throw new \Exception("Access Token Not Generated");
            }

            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/$path";

            $response = Http::withToken($this->token)->{$method}($url, $data);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json()
            ];

        } catch (\Throwable $e) {
            Log::error("🔥 Firestore Request Crash", [
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
