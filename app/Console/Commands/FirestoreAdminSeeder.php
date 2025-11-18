<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Services\FirestoreService;

class FirestoreAdminSeeder extends Command
{
    protected $signature = 'firestore:seed-admin';
    protected $description = 'Seed default admin users into Firestore';

    public function handle()
    {
        $fs = app(FirestoreService::class);

        $admins = [
            [
                'id' => 'admin_01',
                'nama' => 'Admin Utama',
                'email' => 'admin1@ekos.test',
                'password' => Hash::make('admin123'),
            ],
            [
                'id' => 'admin_02',
                'nama' => 'Admin Kos Area',
                'email' => 'admin2@ekos.test',
                'password' => Hash::make('admin456'),
            ],
        ];

        foreach ($admins as $admin) {
            $this->info("Seeding {$admin['email']}...");

            $fs->request('post', "admins?documentId={$admin['id']}", [
                'fields' => [
                    'nama' => ['stringValue' => $admin['nama']],
                    'email' => ['stringValue' => $admin['email']],
                    'password' => ['stringValue' => $admin['password']],
                    'created_at' => ['timestampValue' => now()->toIso8601String()],
                ]
            ]);
        }

        $this->info("=== Seeding Completed ===");
    }
}
