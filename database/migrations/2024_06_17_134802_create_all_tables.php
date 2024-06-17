<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('material');
            $table->string('material_description');
            $table->string('plnt');
            $table->string('sloc');
            $table->string('vendor')->nullable();
            $table->string('bun');
            $table->decimal('begining_qty', 10, 2);
            $table->decimal('begining_value', 15, 2);
            $table->decimal('received_qty', 10, 2)->default(0);
            $table->decimal('received_value', 15, 2)->default(0);
            $table->decimal('consumed_qty', 10, 2)->default(0);
            $table->decimal('consumed_value', 15, 2)->default(0);
            $table->decimal('total_stock', 10, 2)->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            $table->string('currency');
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('part_id');
            $table->string('transaction_type');  // Types: 'received', 'consumed', 'repair_sent', 'repair_returned'
            $table->decimal('quantity', 10, 2);
            $table->decimal('value', 15, 2);
            $table->date('transaction_date');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->foreign('part_id')->references('id')->on('parts')->onDelete('cascade');
        });

        Schema::create('repair_parts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('part_id');
            $table->decimal('sent_for_repair_qty', 10, 2);
            $table->string('repair_status');  // Status: 'sent', 'in_repair', 'repaired'
            $table->decimal('repaired_qty', 10, 2)->default(0);
            $table->date('repair_date');
            $table->date('return_date')->nullable();
            $table->decimal('repair_cost', 15, 2)->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->foreign('part_id')->references('id')->on('parts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repair_parts');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('parts');
    }
}

