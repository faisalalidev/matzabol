<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'email' => 'admin@mefma.com',
            'name' => 'Admin',
            'password' => \Illuminate\Support\Facades\Hash::make('123123'),
            'avatar'=>'https://www.jsweb.uk/images/loginascustomer_profile.jpg',
            'role_id' => 1
        ]);
    }
}
