<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_transaction',function(Blueprint $table){
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->enum('transaction_type',['buy','sell']);
            $table->enum('pair',['idrtrx','idrusdtron','idrbnb','idrbusd','idrbsc']);
            $table->string('transaction_amount');
            $table->string('actual_rate');
            $table->string('transaction_total')->description('tx_amount * actual_rate');
            $table->string('transaction_hash')->unique();
            $table->string('from_address');
            $table->enum('transaction_status',['pending','on progress','success','failed']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
