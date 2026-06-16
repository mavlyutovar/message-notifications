<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

//php artisan db:seed --class=DatabaseSeeder
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Иван Иванов',
                'email' => 'ivan@example.com',
                'password' => Hash::make('password'),
                'phone' => '+79001234567',
            ],
            [
                'name' => 'Мария Петрова',
                'email' => 'maria@example.com',
                'password' => Hash::make('password'),
                'phone' => '+79002345678',
            ],
            [
                'name' => 'Алексей Сидоров',
                'email' => 'alexey@example.com',
                'password' => Hash::make('password'),
                'phone' => '+79003456789',
            ],
            [
                'name' => 'Ольга Смирнова',
                'email' => 'olga@example.com',
                'password' => Hash::make('password'),
                'phone' => '+79004567890',
            ],
            [
                'name' => 'Дмитрий Козлов',
                'email' => 'dmitry@example.com',
                'password' => Hash::make('password'),
                'phone' => '+79005678901',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
