<?php
namespace Database\Seeders;

use App\Models\User3;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder3 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // User3::create([
        //     'username' => 'nome',
        //     'email' => 'email@gmail.com',
        //     'password' => 'senha',
        //     'email_verified_at' => Carbon::now(),
        //     'active' => true
        // ]);
        for ($i = 2; $i <= 4; $i++) {

            User3::create([
                'username' => 'nome' . $i,
                'email' => 'email' . $i . '@gmail.com',
                'password' => 'senha' . $i,
                'email_verified_at' => Carbon::now(),
                'active' => true
            ]);
        }
    }
}
