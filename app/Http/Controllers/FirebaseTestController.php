<?php

namespace App\Http\Controllers;

use Google\Cloud\Firestore\FirestoreClient;

class FirebaseTestController extends Controller
{
    public function test()
    {
        $firestore = new FirestoreClient([
            'projectId' => env('FIREBASE_PROJECT_ID'),
            'keyFilePath' => storage_path(env('FIREBASE_SERVICE_ACCOUNT')),
        ]);

        $docRef = $firestore->collection('test_collection')->document('hello');
        $docRef->set([
            'message' => 'Firestore via gRPC is working!',
            'timestamp' => now()->toDateTimeString()
        ]);

        return "Success!";
    }
}
