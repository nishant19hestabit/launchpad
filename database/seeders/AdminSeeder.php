<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make(12345678),
            'address'=>Str::random(10),
            'profile_picture'=>null,
            'current_school'=>Str::random(6).' '.'public school',
            'previous_school'=>Str::random(6).' '.'public school',
            'role_id'=>1,
            'teacher_assigned'=>0,
        ]);
    }
}
