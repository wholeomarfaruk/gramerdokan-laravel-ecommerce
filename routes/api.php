<?php

use App\Http\Controllers\Api\PathaoCourier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/courier/pathao/webhook', [PathaoCourier::class, 'webhook'])->name('courier.pathao.webhook');
