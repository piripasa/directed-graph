<?php

use Illuminate\Database\Seeder;

class EdgesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        foreach (range(2, 10) as $index) {
            \App\Models\Edge::create([
                'from_node_id' => 1,
                'to_node_id' => $index
            ]);
        }

        foreach (range(3, 20) as $index) {
            \App\Models\Edge::create([
                'from_node_id' => 2,
                'to_node_id' => $index
            ]);
        }

        foreach (range(15, 30) as $index) {
            \App\Models\Edge::create([
                'from_node_id' => 3,
                'to_node_id' => $index
            ]);
        }
    }
}
