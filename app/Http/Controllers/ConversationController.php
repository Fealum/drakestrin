<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $conversations = Message::select('messages.*')
            ->where('sender_user_id', $userId)
            ->orWhere('recipient_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) use ($userId) {
                return $message->sender_user_id === $userId ? $message->recipient_user_id : $message->sender_user_id;
            })
            ->map(function ($messages) use ($userId) {
                return [
                    'latest_message' => $messages->first(),
                    'other_user' => ($messages->first()->sender_user_id === $userId ? $messages->first()->recipient_user_id : $messages->first()->sender_user_id),
                    'message_count' => $messages->count(),
                ];
            })
            ->values();

        $users = User::whereIn('id', $conversations->pluck('other_user'))
            ->get();

        $conversations = $conversations->map(
            function ($message) use ($users) {
                $message['other_user'] = $users->firstWhere('id', $message['other_user']);
                return $message;
            }
        );

        return view('conversation.index', compact('conversations'));
    }

    public function view(User $user): View
    {
        $userAId = auth()->id();
        $userBId = $user->id;

        if ($userAId === $userBId) {
            return to_route('conversation.index');
        }

        $messages = Message::where(function ($query) use ($userAId, $userBId) {
            $query->where('sender_user_id', $userAId)
                ->where('recipient_user_id', $userBId);
        })
            ->orWhere(function ($query) use ($userAId, $userBId) {
                $query->where('sender_user_id', $userBId)
                    ->where('recipient_user_id', $userAId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        Message::whereIn('id', $messages->pluck('id'))
            ->where('recipient_user_id', $userAId)
            ->where('view', 0)
            ->update(['view' => 1]);

        return view('conversation.view', compact('messages', 'user'));
    }

    public function create(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required',
        ]);

        $message = new Message;

        $message->sender_user_id = auth()->id();
        $message->recipient_user_id = $user->id;
        $message->view = 0;
        $message->message = $validated['message'];

        $message->save();

        return to_route('conversation.view', ['user' => $user->id]);
    }
}
