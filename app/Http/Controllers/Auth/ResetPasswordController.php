<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    // Ubah ini dari '/home' menjadi '/dashboard'
    protected $redirectTo = '/dashboard';
}