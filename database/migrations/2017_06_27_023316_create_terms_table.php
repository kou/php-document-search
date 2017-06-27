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
            $table->text('term')->unique();
            $table->text('reading');
            $table->timestamps();
            $table->index([
                              DB::raw('term pgroonga.text_term_search_ops_v2'),
                              DB::raw('reading pgroonga.text_term_search_ops_v2'),
                          ],
                          null,
                          'pgroonga');
        });
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
