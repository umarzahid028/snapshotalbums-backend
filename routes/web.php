<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventReminderMail;
use App\Models\Album;

Route::get('/', function () {
   return response('Welcome to the snapshotalbums. This route is for testing purposes.');

});

Route::get('/test', function () {
    Mail::to('umarzahid028@gmail.com')->send(new EventReminderMail(Album::find(10)));
 });

