<?php

namespace App\Http\Controllers;

use App\Models\Board\Poll;
use App\Models\Board\PollParticipation;
use App\Services\PermissionService;
use App\Support\PollVisibility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PollController extends Controller
{
    public function __construct(PermissionService $permissions)
    {
        parent::__construct($permissions);
    }

    public function vote(Request $request, Poll $poll): RedirectResponse
    {
        abort_unless($request->user(), 403);
        $poll->load('element.post.thread');
        $thread = $poll->element?->post?->thread;
        abort_unless($thread, 404);
        $this->authorize('view', $thread);
        abort_unless($this->permissionService->allows('votepoll', $thread, $request->user()), 403);

        $optionIds = collect($request->validate(['options' => ['required', 'array', 'min:1'], 'options.*' => ['integer']])['options'])
            ->map(fn ($id) => (int) $id)->unique()->values();

        DB::transaction(function () use ($poll, $request, $optionIds) {
            $poll = Poll::query()->whereKey($poll->id)->lockForUpdate()->firstOrFail();
            if ($poll->isClosed()) {
                throw ValidationException::withMessages(['poll' => 'Diese Umfrage ist bereits beendet.']);
            }
            if ($optionIds->count() > $poll->max_choices) {
                throw ValidationException::withMessages(['options' => 'Du hast zu viele Antworten ausgewählt.']);
            }
            if (PollParticipation::query()->where('poll_id', $poll->id)->where('user_id', $request->user()->id)->exists()) {
                throw ValidationException::withMessages(['poll' => 'Du hast an dieser Umfrage bereits teilgenommen.']);
            }

            $options = $poll->options()->whereIn('id', $optionIds)->lockForUpdate()->get();
            if ($options->count() !== $optionIds->count()) {
                throw ValidationException::withMessages(['options' => 'Eine gewählte Antwort gehört nicht zu dieser Umfrage.']);
            }

            $participation = $poll->participations()->create(['user_id' => $request->user()->id]);
            if ($poll->visibility === PollVisibility::OPEN) {
                $participation->choices()->attach($optionIds);
            } else {
                $poll->options()->whereIn('id', $optionIds)->increment('unattributed_votes');
            }
        });

        return back()->with('status', 'Deine Stimme wurde gezählt.');
    }
}
