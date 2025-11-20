<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class FirebaseHelper
{
     protected static $projectId;
     protected static $serviceAccount;

     public static function init()
     {
          self::$serviceAccount = json_decode(
               file_get_contents(storage_path('firebase/firebase_credentials.json')),
               true
          );

          self::$projectId = self::$serviceAccount['project_id'];
     }

     // Fungsi ambil admin berdasarkan email
     public static function getAdminByEmail($email)
     {
          self::init();

          $accessToken = self::getAccessToken();

          $url = "https://firestore.googleapis.com/v1/projects/" . self::$projectId . "/databases/(default)/documents:runQuery";

          $body = [
               "structuredQuery" => [
                    "from" => [["collectionId" => "admins"]],
                    "where" => [
                         "fieldFilter" => [
                              "field" => ["fieldPath" => "email"],
                              "op" => "EQUAL",
                              "value" => ["stringValue" => $email]
                         ]
                    ]
               ]
          ];

          $response = Http::withToken($accessToken)->post($url, $body)->json();

          foreach ($response as $doc) {
               if (isset($doc['document']['fields'])) {
                    $fields = $doc['document']['fields'];
                    return [
                         'nama' => $fields['nama']['stringValue'] ?? null,
                         'email' => $fields['email']['stringValue'] ?? null,
                    ];
               }
          }

          return null;
     }

     // Fungsi generate access token dari service account
     protected static function getAccessToken()
     {
          $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
          $now = time();
          $jwtClaim = base64_encode(json_encode([
               "iss" => self::$serviceAccount['client_email'],
               "scope" => "https://www.googleapis.com/auth/datastore",
               "aud" => "https://oauth2.googleapis.com/token",
               "iat" => $now,
               "exp" => $now + 3600
          ]));

          $signature = '';
          openssl_sign($jwtHeader . '.' . $jwtClaim, $signature, self::$serviceAccount['private_key'], "SHA256");

          $jwt = $jwtHeader . '.' . $jwtClaim . '.' . base64_encode($signature);

          $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
               'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
               'assertion' => $jwt
          ])->json();

          return $response['access_token'] ?? null;
     }
}
