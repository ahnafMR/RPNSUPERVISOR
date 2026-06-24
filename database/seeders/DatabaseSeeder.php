<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Lokasi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@rpn.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
        ]);

        User::create([
            'name' => 'Supervisor Demo',
            'email' => 'supervisor@rpn.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Supervisor,
        ]);

        $lokasis = [
            ['kode_lokasi' => 'GDG-A', 'nama_lokasi' => 'Gudang A', 'latitude' => -6.200000, 'longitude' => 106.816666, 'radius_meter' => 200],
            ['kode_lokasi' => 'GDG-B', 'nama_lokasi' => 'Gudang B', 'latitude' => -6.201000, 'longitude' => 106.817666, 'radius_meter' => 150],
            ['kode_lokasi' => 'WSH-01', 'nama_lokasi' => 'Workshop', 'latitude' => -6.202000, 'longitude' => 106.818666, 'radius_meter' => 100],
            ['kode_lokasi' => 'PRD-01', 'nama_lokasi' => 'Area Produksi', 'latitude' => -6.203000, 'longitude' => 106.819666, 'radius_meter' => 250],
        ];

        foreach ($lokasis as $lokasi) {
            Lokasi::create(array_merge($lokasi, ['status' => 'aktif']));
        }
    }
}
