<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;



class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Ganga Magar',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'api_token' => bcrypt('password')
        ]);
    }
}
