<?php

use App\Livewire\Form;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;

Route::get('form', Form::class);
Route::redirect('login-redirect', 'login')->name('login');

// Emergency structural bypass to build the missing table manually
Route::get('/force-notifications-table', function () {
    try {
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
            
            // Clear all application caches to let Filament see the change instantly
            Artisan::call('optimize:clear');
            
            return '✅ The notifications table has been forcefully built successfully!';
        }
        return 'ℹ️ The notifications table already exists.';
    } catch (\Exception $e) {
        return 'Execution Failed: ' . $e->getMessage();
    }
});
