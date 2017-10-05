<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSimilarSearchIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE INDEX entries_content_similar_search_index " .
                      "ON entries " .
                      "USING pgroonga (id, (title || ' ' || content)) " .
                      "WITH (tokenizer='TokenMecab')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP INDEX entries_content_similar_search_index");
    }
}
