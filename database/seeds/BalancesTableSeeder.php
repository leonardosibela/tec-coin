<?php

use Illuminate\Database\Seeder;

class BalancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('balances')->insert([
            'buyer_id' => '1',
            'admin_id' => '1',
            'description' => 'Tirou MB em todas as matérias',
            'value' => '5',
            "date" => now()
        ]);

        DB::table('balances')->insert([
            'buyer_id' => '1',
            'admin_id' => '1',
            'description' => 'Tirou MB em todas as matérias',
            'value' => '10',
            "date" => now()
        ]);


        DB::table('balances')->insert([
            'buyer_id' => '1',
            'admin_id' => '1',
            'description' => 'Tirou MB em todas as matérias',
            'value' => '8',
            "date" => now()
        ]);


        DB::table('balances')->insert([
            'buyer_id' => '1',
            'admin_id' => '1',
            'description' => 'Tirou MB em todas as matérias',
            'value' => '3',
            "date" => now()
        ]);
    }
}
