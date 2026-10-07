<?php
namespace App\Services\SysIa;
use PDO;
function db(): PDO {
    $p = \Illuminate\Support\Facades\DB::connection()->getPdo();
    $p->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $p;
}
function es_admin(): bool { return auth()->check() && auth()->user()->role === 'admin'; }
function usuario_actual() { return auth()->user(); }
function csrf_ok($token): bool { return is_string($token) && hash_equals(request()->session()->token(), $token); }
function cfg($key, $default = '') { return \App\Models\Setting::where('key', $key)->value('value') ?? $default; }
const APP_NAME = 'Restaurante';
const MONEDA = 'S/';
