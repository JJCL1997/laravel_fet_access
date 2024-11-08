<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('visitors', function (Blueprint $table) {
        $table->string('telefono', 15)->nullable()->after('identificacion');
    });
}

public function down()
{
    Schema::table('visitors', function (Blueprint $table) {
        $table->dropColumn('telefono');
    });
}

};
