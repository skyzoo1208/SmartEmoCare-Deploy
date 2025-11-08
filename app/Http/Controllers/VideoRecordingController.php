<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VideoRecording;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoRecordingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimes:webm,mp4,mov,avi|max:204800', // max ~200MB
        ]);

        $file = $request->file('video');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Simpan ke storage/app/public/videos
        $path = $file->storeAs('videos', $filename, 'public');

        $recording = VideoRecording::create([
            'user_id'  => auth()->user()?->id ?? 1, // fallback jika belum login
            'file_path'=> $path,
        ]);

        return response()->json([
            'status' => 'success',
            'file'   => Storage::url($path),
            'id'     => $recording->id,
        ]);
    }
}
