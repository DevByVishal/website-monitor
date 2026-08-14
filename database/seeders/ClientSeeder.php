<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $client1 = Client::create([
            'email' => 'client1@example.com',
        ]);

        $client1->websites()->createMany([
            [
                'url' => 'https://google.com',
            ],
            [
                'url' => 'https://github.com',
            ],
        ]);

        $client2 = Client::create([
            'email' => 'client2@example.com',
        ]);

        $client2->websites()->createMany([
            [
                'url' => 'https://laravel.com',
            ],
            [
                'url' => 'https://vuejs.org',
            ],
        ]);

        $client3 = Client::create([
            'email' => 'client3@example.com',
        ]);

        $client3->websites()->createMany([ // for down website
            [
                'url' => 'https://laravel1.com',
            ],
        ]);
    }
}