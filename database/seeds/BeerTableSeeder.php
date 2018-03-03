<?php

use Illuminate\Database\Seeder;

class BeerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('beers')->insert([
        	'id_flavour' => mt_rand(1, 30),
        	'name' => str_random(15),
        	'name2' => str_random(15)
        	]);
    }
}
