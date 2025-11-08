<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SoundTherapyController extends Controller
{
    public function index(User $user)
    {
        return view('soundtherapy', compact('user'));
    }
}
