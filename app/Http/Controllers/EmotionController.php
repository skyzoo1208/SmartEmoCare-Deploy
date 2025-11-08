<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmotionRecord;

class EmotionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'    => 'required|integer|exists:users,id',
            'emotion'    => 'required|string',
            'confidence' => 'required|numeric',
            'frame_time' => 'required|date',
        ]);

        // Tidak perlu pakai \App\Models\ lagi karena sudah di 'use'
        $record = EmotionRecord::create($data);

        return response()->json([
            'status' => 'success',
            'data'   => $record,
        ]);
    }
}
