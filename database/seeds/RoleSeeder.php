<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'id' => 1,
            'title' => 'admin'
        ]);

        DB::table('roles')->insert([
            'id' => 2,
            'title' => 'Corporate'
        ]);
        DB::table('roles')->insert([
            'id' => 3,
            'title' => 'Individual'
        ]);
    }
}
