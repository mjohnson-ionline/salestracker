<?php

namespace Database\Seeders;

use App\Models\Reseller;
use Illuminate\Database\Seeder;

class ResellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Reseller::factory()->count(5)->create();
    }
}
