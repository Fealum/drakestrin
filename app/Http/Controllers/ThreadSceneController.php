<?php

namespace App\Http\Controllers;

use App\Models\Board\Thread;
use App\Models\Board\ThreadScene;
use App\Models\Territory\Location;
use App\Services\PermissionService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ThreadSceneController extends Controller
{
    public function __construct(PermissionService $permissionService)
    {
        parent::__construct($permissionService);
    }

    public function create(Request $request, Thread $thread): View|RedirectResponse
    {
        $this->authorize('setScene', $thread);
        $thread->load('currentScene.location');

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'location_id' => ['required', 'integer', 'exists:locations,id'],
                'story_started_at' => ['nullable', 'date_format:Y-m-d\TH:i'],
            ]);

            DB::transaction(function () use ($request, $thread, $data) {
                $anchorPostId = $thread->last_post_id ?: null;
                $storyStartedAt = $this->timestampFromDatetimeLocal($data['story_started_at'] ?? null);

                $this->endOpenScenes($thread, $anchorPostId, null);

                ThreadScene::create([
                    'thread_id' => $thread->id,
                    'location_id' => (int) $data['location_id'],
                    'starts_at_post_id' => $anchorPostId,
                    'story_started_at' => $storyStartedAt,
                    'created_by_user_id' => $request->user()->id,
                ]);
            });

            return redirect()->route('thread.view', ['thread' => $thread->id]);
        }

        return view('thread-scene.create', [
            'locations' => $this->locationOptions(),
            'thread' => $thread,
        ]);
    }

    public function end(Request $request, Thread $thread): View|RedirectResponse
    {
        $this->authorize('endScene', $thread);
        $thread->load('currentScene.location');

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'story_ended_at' => ['nullable', 'date_format:Y-m-d\TH:i'],
            ]);

            $this->endOpenScenes(
                $thread,
                $thread->last_post_id ?: null,
                $this->timestampFromDatetimeLocal($data['story_ended_at'] ?? null),
            );

            return redirect()->route('thread.view', ['thread' => $thread->id]);
        }

        return view('thread-scene.end', [
            'thread' => $thread,
        ]);
    }

    private function endOpenScenes(Thread $thread, ?int $postId, ?int $storyEndedAt): void
    {
        ThreadScene::query()
            ->where('thread_id', $thread->id)
            ->whereNull('ended_at')
            ->update([
                'ends_at_post_id' => $postId,
                'story_ended_at' => $storyEndedAt,
                'ended_at' => now(),
            ]);
    }

    private function timestampFromDatetimeLocal(?string $value): ?int
    {
        if (! filled($value)) {
            return null;
        }

        return CarbonImmutable::createFromFormat('Y-m-d\TH:i', $value, config('app.timezone'))->timestamp;
    }

    private function locationOptions()
    {
        return Location::query()
            ->orderBy('priority')
            ->orderByRaw('LOWER(name)')
            ->get();
    }
}
