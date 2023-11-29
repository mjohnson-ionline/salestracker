<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder

{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'first_name' => "Matthew",
            'last_name' => "Johnson",
            'email' => "matthew@ionline.com.au",
            'password' => Hash::make('7*HSgT1!xB#%C2eC1bDHtQzGG6z@A9W2R'),
            'created_at' => Carbon::now(),

        ]);
        DB::table('users')->insert([
            'first_name' => "Reinhard",
            'last_name' => "Navarro",
            'email' => "rnavarro@ionline.com.au",
            'password' => Hash::make('#x84c!f^JS5nKXXLzJiafj7m6iepJv6Qu'),
            'created_at' => Carbon::now(),
        ]);
	    DB::table('users')->insert([
		    'first_name' => "Rodney",
		    'last_name' => "Armstrong",
		    'email' => "rarmstrong@ionline.com.au",
		    'password' => Hash::make('fSuN4x7YtO!uTjc4%mp%Vh#owL$QdILCO'),
		    'created_at' => Carbon::now(),
	    ]);
	    DB::table('users')->insert([
		    'first_name' => "Shaia",
		    'last_name' => "Deacon",
		    'email' => "shaia@ionline.com.au",
		    'password' => Hash::make('fSuN4x7YtO!uTjc4%mp%Vh#owL$QdILCO'),
		    'created_at' => Carbon::now(),
	    ]);
	    DB::table('users')->insert([
		    'first_name' => "Jason",
		    'last_name' => "Deacon",
		    'email' => "jason@ionline.com.au",
		    'password' => Hash::make('96u!f3^bJ3C2zBFlRDeyfR6Q6n@6KyAf%'),
		    'created_at' => Carbon::now(),
	    ]);
	    DB::table('users')->insert([
		    'first_name' => "Melissa",
		    'last_name' => "MacDonald",
		    'email' => "melissa@ionline.com.au",
		    'password' => Hash::make('@tN!MEUrg&BtNuU&^1q^4n1z3qxiU*0vP'),
		    'created_at' => Carbon::now(),
	    ]);
        DB::table('users')->insert([
            'first_name' => "Stefan",
            'last_name' => "Jeanin",
            'email' => "stefan@ionline.com.au",
            'password' => Hash::make('96u!f3^bJ3C2zBFlRDeyfR6Q6n@6KyAf%'),
            'created_at' => Carbon::now(),
        ]);
    }
}
