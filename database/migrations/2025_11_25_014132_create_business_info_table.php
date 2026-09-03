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
        Schema::create('business_info', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('business_slug')->nullable();
            $table->string('business_short_name')->nullable();
            $table->string('business_domain')->nullable();
            $table->string('business_allowed_file_types')->nullable();
            $table->string('business_logo')->nullable();
            $table->string('business_logo_dark')->nullable();
            $table->string('business_favicon')->nullable();
            $table->string('business_address')->nullable();
            $table->string('business_address2')->nullable();
            $table->string('business_city')->nullable();
            $table->string('business_state')->nullable();
            $table->string('business_zip')->nullable();
            $table->string('business_country')->nullable();
            $table->string('business_phone')->nullable();
            $table->string('business_email')->nullable();
            $table->string('business_website')->nullable();
            $table->string('business_registration_number')->nullable();
            $table->string('business_registration_number2')->nullable();
            $table->date('business_start_date')->nullable();
            $table->string('business_status');

            $table->string('business_brand_app_name')->default('NEOERP');
            $table->text('business_brand_logo_on_top_left')->default('<span class="app-brand-logo demo">
                <span class="text-primary">
                    <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="currentColor"></path>
                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616"></path>
                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="currentColor"></path>
                    </svg>
                </span>
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-3">NEOERP</span>')->nullable(false);
            $table->tinyInteger('business_brand_credit_on_footer_enable')->default(1)->nullable(false);
            $table->text('business_brand_credit_on_footer')->default('©
                                    <script>
                                        document.write(new Date().getFullYear());
                                    </script>
                                    , made with ❤️ by <a href="https://neotericdigital.co.uk/" target="_blank" class="footer-link">Neoteric Digital</a>')->nullable(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_info');
    }
};
