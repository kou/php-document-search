<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->increments('id');
            $table->text('term');
            $table->text('label');
            $table->text('reading');
            $table->timestamps();
            $table->index([
                              DB::raw('reading pgroonga_text_term_search_ops_v2'),
                          ],
                          null,
                          'pgroonga');
        });
        DB::statement("CREATE INDEX terms_term_index " .
                      "ON terms " .
                      "USING pgroonga (term) " .
                      "WITH (tokenizer='TokenBigramSplitSymbolAlphaDigit')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terms');
    }
}
