<?php

use Illuminate\Database\Seeder;

class SynonymsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $synonyms = [
            ["term" => "@", "synonym" => "@"],
            ["term" => "@", "synonym" => ">エラー制御演算子"],
        ];
        DB::table("synonyms")->insert($synonyms);
    }
}
