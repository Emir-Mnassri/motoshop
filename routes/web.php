<?php

use App\Livewire\Form;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Your existing app routes
Route::get('form', Form::class);
Route::redirect('login-redirect', 'login')->name('login');

/*
|--------------------------------------------------------------------------
| The Nuclear Database Rebuild Route
|--------------------------------------------------------------------------
| Run this once in your browser to wipe out the corrupt structure and
| build a pristine database from scratch. Delete it after use!
*/
Route::get('/nuclear-rebuild', function () {
    try {
        // 1. Wipe the DB completely and run ALL migrations from scratch cleanly
        Artisan::call('migrate:fresh', ['--force' => true]);
        
        // 2. Re-create your Admin user inside the fresh database structure
        User::create([
            'name' => 'Admin',
            'email' => 'admin@motoshop.com',
            'password' => Hash::make('NuclearShop2026!'),
        ]);
        
        // 3. Re-publish Filament assets and clear caches
        Artisan::call('optimize:clear');
        Artisan::call('filament:upgrade');

        return '💥 Database wiped, rebuilt cleanly, and Admin user registered!';
    } catch (\Exception $e) {
        return 'Execution Failed: ' . $e->getMessage();
    }
});
