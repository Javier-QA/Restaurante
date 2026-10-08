<?php

use Illuminate\Support\Facades\Route;

// Chatbot y Asistente IA originales de SYS, integrados con Laravel.
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::view('/ai/assistant', 'assistant.index')->name('ai.assistant');
    Route::view('/ai/chat', 'assistant.chat')->name('ai.chat');
    Route::redirect('/ai/settings', '/asistente-ia?configurar=1')->name('ai.settings');
    Route::view('/asistente-ia', 'assistant.index')->name('assistant.index');
    Route::view('/chatbot', 'assistant.chat')->name('chatbot.index');
    Route::match(['get', 'post'], '/asistente-ia/api', [\App\Http\Controllers\SysIaController::class, 'assistant'])->middleware('throttle:60,1')->name('assistant.api');
    Route::match(['get', 'post'], '/chatbot/api', [\App\Http\Controllers\SysIaController::class, 'chat'])->middleware('throttle:60,1')->name('chatbot.api');
});
