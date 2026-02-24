<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReminderController;

Route::get('/', [ReminderController::class, 'index'])->name('home');
Route::post('/store', [ReminderController::class, 'store'])->name('reminders.store');
Route::get('/snooze/{id}', [ReminderController::class, 'snooze'])->name('reminders.snooze');
Route::post('/snooze-custom/{id}', [ReminderController::class, 'snoozeCustom'])->name('reminders.snooze-custom');
Route::delete('/delete/{id}', [ReminderController::class, 'delete'])->name('reminders.delete');
Route::post('/complete/{id}', [ReminderController::class, 'markAsCompleted'])->name('reminders.complete');