<?php

use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('ticket',[TicketController::class,'initTicket']);

