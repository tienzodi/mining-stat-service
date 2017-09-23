<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Services\PushNotifications;

class HomeController extends Controller
{
    public function index()
    {
        return 'Hello';
    }

    public function sendNotification() {
        PushNotifications::sendNotificationToMobile('XXX', 'XXX');
        return "Hello form send notification";
    }
}
