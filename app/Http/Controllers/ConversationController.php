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

    function seems_utf8($str)
    {
        $length = strlen($str);
        for ($i = 0; $i < $length; $i++) {
            $c = ord($str[$i]);
            if ($c < 0x80) continue; // ASCII (0-127)

            // Multibyte-Sequenzen (UTF-8)
            if (($c & 0xE0) === 0xC0) $n = 1; // 2-byte sequence
            elseif (($c & 0xF0) === 0xE0) $n = 2; // 3-byte sequence
            elseif (($c & 0xF8) === 0xF0) $n = 3; // 4-byte sequence
            else return false; // Ungültiges UTF-8-Zeichen

            // Überprüfe die nächsten n Bytes
            for ($j = 0; $j < $n; $j++) {
                if (++$i == $length || (ord($str[$i]) & 0xC0) !== 0x80)
                    return false; // Folgebytes entsprechen nicht dem Muster
            }
        }
        return true; // Scheint gültiges UTF-8 zu sein
    }

    function fix_encoding($str)
    {
        return $str;

        return mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
        if ($this->seems_utf8(mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8'))) return 'unkonvertiert:' . $str; // Keine Konvertierung nötig
        else return 'utf8konv' . mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8'); // Konvertiere von ISO-8859-1 nach UTF-8
    }

    function convertToUTF8($message)
    {
        $message = mb_convert_encoding($message, 'ISO-8859-1', 'UTF-8');
        if (!mb_check_encoding($message, 'ISO-8859-1')) {
            // Versuche zu ermitteln, ob die Zeichenkette ISO-8859-1 ist
            if (mb_check_encoding($message, '')) {
                // Konvertiere von ISO-8859-1 nach UTF-8
                return mb_convert_encoding($message, 'UTF-8', 'ISO-8859-1');
            } else {
                dd($message);
            }
        }
        return 'ISO-8859-1: ' . $message; // Keine Konvertierung nötig, da bereits UTF-8
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
