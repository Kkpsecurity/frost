<?php

/**
 * Account Profile
 */
Route::get('account/{page?}', [App\Http\Controllers\Web\Account\UserAccountController::class, 'dashboard'])
    ->where('page', '(dashboard|profile|password|avatar|billing)?')
    ->name('account');

Route::put('account/profile/update', [App\Http\Controllers\Web\Account\UserAccountController::class, 'updateProfile'])
    ->name('account.profile.update');

Route::post('account/password/update', [App\Http\Controllers\Web\Account\UserAccountController::class, 'updatePassword'])
    ->name('account.password.update');

Route::post('account/avatar/upload', [App\Http\Controllers\Web\Account\UserAccountController::class, 'uploadAvatar'])
    ->name('account.avatar.upload');

Route::get('account/avatar/delete', [App\Http\Controllers\Web\Account\UserAccountController::class, 'removeAvatar'])
    ->name('account.avatar.delete');

Route::post('account/update/gravatar', [App\Http\Controllers\Web\Account\UserAccountController::class, 'updateGravatar'])
    ->name('account.update.gravatar');
    
Route::get('account/settings', [App\Http\Controllers\Web\Account\UserAccountController::class, 'settings'])
    ->name('account.settings');
