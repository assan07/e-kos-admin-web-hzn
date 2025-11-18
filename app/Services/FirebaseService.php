<?php

namespace App\Services;

use GuzzleHttp\Client;
use Firebase\JWT\JWT;

class FirebaseService
{
     private $projectId;
     private $clientEmail;
     private $privateKey;
     private $http;

     public function __construct()
     {
          $this->projectId = env('FIREBASE_PROJECT_ID');
          $this->clientEmail = env('FIREBASE_CLIENT_EMAIL');
          $this->privateKey = str_replace("\\n", "\n", env('FIREBASE_PRIVATE_KEY'));
          $this->http = new Client();
     }

     private function getAccessToken()
     {
          $now = time();
          $payload = [
               "iss" => $this->clientEmail,
               "sub" => $this->clientEmail,
               "aud" => "https://oauth2.googleapis.com/token",
               "iat" => $now,
               "exp" => $now + 3600,
               "scope" => "https://www.googleapis.com/auth/datastore"
          ];

          $jwt = JWT::encode($payload, $this->privateKey, 'RS256');

          $response = $this->http->post("https://oauth2.googleapis.com/token", [
               "form_params" => [
                    "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
                    "assertion" => $jwt
               ]
          ]);

          return json_decode($response->getBody(), true)['access_token'];
     }

     public function addDocument($collection, $data)
     {
          $token = $this->getAccessToken();

          $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}";

          $body = [
               "fields" => $this->formatFields($data)
          ];

          $response = $this->http->post($url, [
               "headers" => [
                    "Authorization" => "Bearer $token",
                    "Content-Type" => "application/json"
               ],
               "json" => $body
          ]);

          return json_decode($response->getBody(), true);
     }

     private function formatFields($data)
     {
          $formatted = [];
          foreach ($data as $key => $value) {
               $formatted[$key] = ["stringValue" => $value];
          }
          return $formatted;
     }
}
