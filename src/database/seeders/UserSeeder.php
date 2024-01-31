<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(2)
            ->state(new Sequence([
                'email' => 'user@gmail.com',
                'password' => 'testuser'
            ], [
                'email' => 'user2@gmail.com',
                'password' => 'testuser2'
            ]))
            ->create();
    }
}
