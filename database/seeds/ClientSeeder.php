<?php

use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //iOS
        DB::table('clients')->insert([
            'client_id' => 'mefma-app-ios',
            'client_secret' => 'YWhsYW0tYXBwLWlvczo1Nzk3ZTY2Mi0wYzQ0LTRjYWYtOGU1OS01OGUwNzVjOWI3NGI=',
        ]);

        //Android
        DB::table('clients')->insert([
            'client_id' => 'mefma-app-android',
            'client_secret' => 'YWhsYW0tYXBwLWFuZHJvaWQ6NGQxNjNlZTgtMzJiZi00M2U2LWFlMzgtY2E1YmMwZjA0N2Nk',
        ]);

    }
}
