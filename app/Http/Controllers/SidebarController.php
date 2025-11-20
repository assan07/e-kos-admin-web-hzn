<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\FirebaseRestService;
use Illuminate\Support\Facades\Log;

class SidebarController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseRestService $firebase)
    {
        $this->firebase = $firebase;
    }

    // Fetch kamar kos via AJAX
    public function fetchKamar($idKos)
    {
        try {
            $kamarDocuments = $this->firebase->fetchSubcollection($idKos, 'kamar');
            $kamarList = [];
            foreach ($kamarDocuments as $kamarDoc) {
                $kamarList[] = [
                    'id_kamar' => basename($kamarDoc['name']),
                    'nama_kamar' => $kamarDoc['fields']['nama_kamar']['stringValue'] ?? 'Kamar tanpa nama',
                ];
            }
            return response()->json($kamarList);
        } catch (\Exception $e) {
            Log::error("Fetch kamar error: " . $e->getMessage());
            return response()->json([], 500);
        }
    }
    

    
}
