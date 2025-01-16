<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Files extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->nullableMorphs('assoc'); // 关联资源
            $table->string("name")->nullable();
            $table->string("path", 1024);
            $table->string("mime_type")->nullable();
            $table->string('ext')->nullable();
            $table->bigInteger("size");
            $table->string("storage");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('files');
    }
}
