<?php

namespace App\Services;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;

class FirebaseTokenService
{
     public function createToken()
     {
          $clientEmail = env('FIREBASE_CLIENT_EMAIL');
          $privateKey  = str_replace("\\n", "\n", env('FIREBASE_PRIVATE_KEY'));

          $payload = [
               "iss"   => $clientEmail,
               "sub"   => $clientEmail,
               "aud"   => "https://oauth2.googleapis.com/token",
               "iat"   => time(),
               "exp"   => time() + 3600,
               "scope" => "https://www.googleapis.com/auth/datastore"
          ];

          $jwt = JWT::encode($payload, $privateKey, 'RS256');

          $client = new Client();
          $response = $client->post("https://oauth2.googleapis.com/token", [
               "form_params" => [
                    "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
                    "assertion"  => $jwt,
               ]
          ]);

          return json_decode($response->getBody(), true)['access_token'];
     }
}
