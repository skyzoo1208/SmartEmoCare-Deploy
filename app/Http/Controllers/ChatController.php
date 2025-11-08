<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index(User $user) {
        $messages = Message::where('user_id', $user->id)->get();
        return view('chat', compact('user', 'messages'));
    }

    public function send(User $user, Request $request) {
        $input = $request->input('message');
        Message::create([
            'user_id' => $user->id,
            'role' => 'user',
            'content' => $input,
        ]);

        $history = Message::where('user_id', $user->id)->get()->map(fn($m) => [
            'role' => $m->role,
            'content' => $m->content
        ])->toArray();

        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => $history,
                            ]);

        $reply = $response['choices'][0]['message']['content'];
        Message::create([
            'user_id' => $user->id,
            'role' => 'assistant',
            'content' => $reply,
        ]);

        return back();
    }
}
