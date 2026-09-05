<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_addons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedSmallInteger('duration_minutes')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('service_service_addon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_addon_id')->constrained('service_addons')->cascadeOnDelete();
            $table->unique(['service_id', 'service_addon_id']);
        });

        Schema::create('booking_service_addon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_addon_id')->nullable()->constrained('service_addons')->nullOnDelete();
            $table->string('name');
            $table->decimal('unit_price', 10, 2);
            $table->unsignedSmallInteger('duration_minutes')->default(0);
            $table->unsignedTinyInteger('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('filename');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('alt')->nullable();
            $table->string('folder')->default('media');
            $table->timestamps();

            $table->index(['folder', 'created_at']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('sms_reminder_sent_at')->nullable()->after('cancelled_at');
            $table->timestamp('sms_confirmation_sent_at')->nullable()->after('sms_reminder_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['sms_reminder_sent_at', 'sms_confirmation_sent_at']);
        });

        Schema::dropIfExists('media');
        Schema::dropIfExists('booking_service_addon');
        Schema::dropIfExists('service_service_addon');
        Schema::dropIfExists('service_addons');
    }
};
