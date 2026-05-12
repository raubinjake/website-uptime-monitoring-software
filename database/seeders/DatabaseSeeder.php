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
        $clients = [
            'client@example.com' => [
                'https://laravel.com',
                'https://vuejs.org',
            ],
            'operations@example.com' => [
                'https://github.com',
                'https://www.php.net',
                'https://www.mysql.com',
            ],
            'support@example.com' => [
                'https://example.com',
                'https://www.wikipedia.org',
            ],
            'engineering@example.com' => [
                'https://developer.mozilla.org',
                'https://www.npmjs.com',
                'https://packagist.org',
            ],
        ];

        foreach ($clients as $email => $urls) {
            $client = Client::query()->firstOrCreate(['email' => $email]);

            foreach ($urls as $url) {
                $client->websites()->firstOrCreate(['url' => $url]);
            }
        }
    }
}
