<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ConfirmsPasswords;

class ConfirmPasswordController extends Controller
{
    use ConfirmsPasswords;

    // Ubah ini dari '/home' menjadi '/dashboard'
    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('auth');
    }
}