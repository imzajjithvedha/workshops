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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained();
            $table->string('attendee_name');
            $table->string('attendee_email');
            $table->string('status', 20)->default('active');
            $table->foreignId('registered_by')->constrained('users');
            $table->timestamp('registered_at');
            $table->foreignId('cancelled_by')->nullable()->constrained('users');
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();

            $table->string('active_email')->nullable()->storedAs("case when status = 'active' then attendee_email end");

            $table->index(['workshop_id', 'status']);
            $table->unique(['workshop_id', 'active_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
