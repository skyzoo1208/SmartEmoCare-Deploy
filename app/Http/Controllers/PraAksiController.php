<?php

namespace App\Http\Controllers;

use App\Models\User;

class PraAksiController extends Controller
{
    public function index(User $user)
    {
        return view('pra_aksi', compact('user'));
    }
}
