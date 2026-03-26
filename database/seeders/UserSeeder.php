<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role; // Import Model Role dari Spatie

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Roles
        // Pastikan role belum ada sebelum dibuat untuk mencegah error jika di-seed ulang
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // 2. Buat Akun Admin Utama
        $admin = User::firstOrCreate(
            ['email' => 'admin@keluargaku.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
            ]
        );
        // Assign role admin ke user ini
        $admin->assignRole($adminRole);

        // 3. Buat Akun User Biasa (Perwakilan Keluarga)
        $budi = User::firstOrCreate(
            ['email' => 'budi@keluargaku.com'],
            [
                'name' => 'Budi Perwakilan',
                'password' => Hash::make('password123'),
            ]
        );
        // Assign role user ke user ini
        $budi->assignRole($userRole);
    }
}
