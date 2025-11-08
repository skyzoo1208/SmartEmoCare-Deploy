<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FaceDetectionController extends Controller
{
    public function index()
    {
        return view('facedetection'); // pastikan nama file Blade-nya facedetection.blade.php
    }
}
