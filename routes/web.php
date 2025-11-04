<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
   return response('Welcome to the snapshotalbums. This route is for testing purposes.');

});

Route::get('/test', function () {
    Mail::to('umarzahid02*@gmail.com')->send(new EventReminderMail(Album::find(1)));
 });

