<?php
 
namespace Database\Seeders;
 
use App\Models\User;
use App\Models\Reseller;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
 
class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Owner account
        User::updateOrCreate(
            ['email' => 'owner@lebaran.com'],
            [
                'name'              => 'Owner',
                'email'             => 'owner@lebaran.com',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
 
        $this->command->info('✅ Owner account created: owner@lebaran.com / password');

        // Buat default reseller untuk customer langsung (non-reseller)
        Reseller::firstOrCreate(
            ['nama_reseller' => 'Tanpa Reseller (Direct)']
        );

        $this->command->info('✅ Default Reseller "Tanpa Reseller (Direct)" created');
    }
}
