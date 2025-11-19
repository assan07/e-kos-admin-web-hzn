<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

class FirebaseUploader
{
     protected $client;
     protected $bucket;

     public function __construct()
     {
          $this->client = new Client();
          $this->bucket = env('FIREBASE_STORAGE_BUCKET'); // e-kos-9b7ee.firebasestorage.app
     }

     public function upload($localPath, $remotePath)
     {
          $serviceAccount = json_decode(
               file_get_contents(storage_path('app/firebase/service-account.json')),
               true
          );

          // Membuat signed URL manual
          $expiration = time() + 3600;

          $policy = base64_encode(json_encode([
               'expiration' => gmdate('Y-m-d\TH:i:s\Z', $expiration),
               'conditions' => [
                    ['bucket' => $this->bucket],
                    ['key' => $remotePath],
                    ['acl' => 'public-read']
               ]
          ]));

          $credentials = new ServiceAccountCredentials(
               'https://www.googleapis.com/auth/devstorage.full_control',
               $serviceAccount
          );

          $signature = $credentials->signBlob($policy);

          $url = "https://storage.googleapis.com/{$this->bucket}/{$remotePath}";

          // Upload file via PUT
          $this->client->put($url, [
               'body' => fopen($localPath, 'r')
          ]);

          return $url;
     }
}
