<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGastosDeEnvioToConfigGeneralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'config_general', function (Blueprint $table) {
                $table->decimal('gastos_de_envio', 8, 2)->default(0);
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'config_general', function (Blueprint $table) {
                $table->dropColumn('gastos_de_envio');
            }
        );
    }
}
