<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\ContactController::class, 'index'])->name('contacts');
Route::get('/contact/list', [\App\Http\Controllers\ContactController::class, 'list'])->name('contacts.list');
Route::get('/contact/add', [\App\Http\Controllers\ContactController::class, 'add'])->name('contact.add');
Route::post('/contact/store', [\App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');
Route::get('/contact/edit/{id?}', [\App\Http\Controllers\ContactController::class, 'edit'])->name('contact.edit');
Route::post('/contact/update/{id}', [\App\Http\Controllers\ContactController::class, 'update'])->name('contact.update');
Route::post('/contact/delete/{id?}', [\App\Http\Controllers\ContactController::class, 'delete'])->name('contact.delete');
Route::post('/contact/import', [\App\Http\Controllers\ContactController::class, 'importContacts'])->name('contact.import');

