<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Http\Request;
use App\Message;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::where(function ($query) {
            $query->where('form', request('form'));
            $query->where('to', request('to'));
        })->orWhere(function ($query) {
            $query->where('form', request('to'));
            $query->where('to', request('form'));
        })->oldest()->get()->load(['from', 'to']);

        return response()->json(compact('messages'), 200);
    }

    public function store()
    {
        $validated = request()->validate([
            'text' => 'required',
            'to' => 'required',
            'form' => 'required',
        ]);

        $message = Message::create($validated)->load(['from', 'to']);

        MessageSent::dispatch($message);

        return response()->json(compact('message'), 201);
    }
}
