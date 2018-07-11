<?php

use Illuminate\Database\Seeder;

class NodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        foreach (range(1, 50) as $index) {
            \App\Models\Node::create([
                'name' => $faker->name
            ]);
        }
    }
}
