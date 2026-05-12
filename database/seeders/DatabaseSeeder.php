<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $client = Client::query()->firstOrCreate([
            'email' => 'client@example.com',
        ]);

        foreach (['https://laravel.com', 'https://vuejs.org'] as $url) {
            $client->websites()->firstOrCreate(['url' => $url]);
        }
    }
}
