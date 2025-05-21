<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->string('group')->default('general');
            $table->timestamps();
        });
        
        // Insert default settings
        $this->seedDefaultSettings();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
    
    /**
     * Seed default settings
     */
    private function seedDefaultSettings(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Sistem Tracer Study & Rekomendasi Karir',
                'type' => 'string',
                'group' => 'general'
            ],
            [
                'key' => 'site_description',
                'value' => 'Sistem Pelacakan Alumni dan Rekomendasi Karir untuk SMK',
                'type' => 'string',
                'group' => 'general'
            ],
            [
                'key' => 'school_name',
                'value' => 'SMKN 1 Terisi',
                'type' => 'string',
                'group' => 'school'
            ],
            [
                'key' => 'school_address',
                'value' => 'Jl. Raya Terisi No. 1, Indramayu',
                'type' => 'string',
                'group' => 'school'
            ],
            [
                'key' => 'school_phone',
                'value' => '(021) 1234567',
                'type' => 'string',
                'group' => 'school'
            ],
            [
                'key' => 'school_email',
                'value' => 'info@smkn1terisi.sch.id',
                'type' => 'string',
                'group' => 'school'
            ],
            [
                'key' => 'logo_path',
                'value' => 'logo/logo.png',
                'type' => 'image',
                'group' => 'appearance'
            ],
            [
                'key' => 'favicon_path',
                'value' => 'logo/favicon.ico',
                'type' => 'image',
                'group' => 'appearance'
            ],
            [
                'key' => 'primary_color',
                'value' => '#4B49AC',
                'type' => 'string',
                'group' => 'appearance'
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'system'
            ],
        ];
        
        DB::table('settings')->insert($settings);
    }
};
