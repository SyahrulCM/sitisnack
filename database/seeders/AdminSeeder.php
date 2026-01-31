<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama_lengkap' => 'Admin SitiSnack',
                'email' => 'admin@sitisnack.test',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
                'status_aktif' => true,
            ]
        );
    }
}
