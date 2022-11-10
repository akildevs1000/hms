<?php

namespace App\Http\Controllers;

use App\Mail\CompanyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SingleController extends Controller
{
    public function testMail()
    {
        $message = [
            'message'=>'You have been successfully created company',
        ];

        Mail::to('test@test.com')->send(new CompanyMail($message));

    }
}
