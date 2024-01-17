<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $conversations = Conversation::select('dra_conversation.*')
            ->where('user__sender', $userId)
            ->orWhere('user__recipient', $userId)
            ->orderBy('time', 'desc')
            ->get()
            ->groupBy(function ($message) use ($userId) {
                return $message->user__sender === $userId ? $message->user__recipient : $message->user__sender;
            })
            ->map(function ($messages) use ($userId) {
                return [
                    'latest_message' => $messages->first(),
                    'other_user' => ($messages->first()->user__sender === $userId ? $messages->first()->user__recipient : $messages->first()->user__sender),
                    'message_count' => $messages->count(),
                ];
            })
            ->values();

        $users = User::whereIn('id', $conversations->pluck('other_user'))
            ->get();

        $conversations = $conversations->map(
            function ($conversation) use ($users) {
                $conversation['other_user'] = $users->firstWhere('id', $conversation['other_user']);
                return $conversation;
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

        $conversations = Conversation::where(function ($query) use ($userAId, $userBId) {
            $query->where('user__sender', $userAId)
                ->where('user__recipient', $userBId);
        })
            ->orWhere(function ($query) use ($userAId, $userBId) {
                $query->where('user__sender', $userBId)
                    ->where('user__recipient', $userAId);
            })
            ->orderBy('time', 'desc')
            ->get();

        Conversation::whereIn('id', $conversations->pluck('id'))
            ->where('user__recipient', $userAId)
            ->where('view', 0)
            ->update(['view' => 1]);

        return view('conversation.view', compact('conversations', 'user'));
    }

    public function create(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required',
        ]);

        $conversation = new Conversation;

        $conversation->user__sender = auth()->id();
        $conversation->user__recipient = $user->id;
        $conversation->view = 0;
        $conversation->message = $validated['message'];

        $conversation->save();

        return to_route('conversation.view', ['user' => $user->id]);
    }
}
