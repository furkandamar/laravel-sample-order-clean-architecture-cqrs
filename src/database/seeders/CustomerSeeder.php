<?php

namespace Database\Seeders;

use App\Models\CustomerModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        CustomerModel::create([
            'name' => 'Türker Jöntürk',
            'email' => 'turker@mail.com',
            'password' => Hash::make('abc123'),
            'balance' => 1500
        ]);

        CustomerModel::create([
            'name' => 'Kaptan Devopuz',
            'email' => 'kaptan@mail.com',
            'password' => Hash::make('cba321'),
            'balance' => 100
        ]);

        CustomerModel::create([
            'name' => 'İsa Sonuyumaz',
            'email' => 'isa@mail.com',
            'password' => Hash::make('bca231'),
            'balance' => 1000
        ]);

        CustomerModel::create([
            'name' => 'Cumali Karacuma',
            'email' => 'cumali@mail.com',
            'password' => Hash::make('cab312'),
            'balance' => 10000
        ]);

        CustomerModel::create([
            'name' => 'Eren Maskoğlu',
            'email' => 'eren@mail.com',
            'password' => Hash::make('acb132'),
            'balance' => 1
        ]);
    }
}
