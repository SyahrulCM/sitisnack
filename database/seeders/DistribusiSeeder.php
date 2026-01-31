<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DistribusiSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'distribusi'],
            [
                'nama_lengkap' => 'Akun Distribusi',
                'email' => 'distribusi@sitisnack.test',
                'password' => Hash::make('distribusi12345'),
                'role' => 'distribusi',
                'status_aktif' => true,
            ]
        );
    }
}
