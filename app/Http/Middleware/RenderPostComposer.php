<?php

namespace App\Http\Middleware;

use App\Services\Board\PostComposerViewData;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class RenderPostComposer
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('HX-Request') !== 'true' || ! $request->isMethod('post')) {
            return $next($request);
        }

        $startedAt = hrtime(true);
        $response = $next($request);

        if (! $response instanceof RedirectResponse) {
            return $response;
        }

        if ($request->input('intent') === 'publish' && ! $request->attributes->get('composer.markers_expanded')) {
            return response('', 200)->header('HX-Redirect', $response->getTargetUrl());
        }

        $draft = $request->attributes->get('composer.draft');
        if (! $draft) {
            return $response;
        }
        if (! $draft->exists && $request->session()->hasOldInput('elements')) {
            $draft->payload = $request->session()->getOldInput('elements');
        }
        $thread = $request->attributes->get('composer.thread');
        $data = app(PostComposerViewData::class)->make($draft, $request->user(), $thread);
        $data['threadPage'] = max(1, $request->integer('thread_page', 1));
        $request->session()->forget(['_old_input', 'status']);

        return response()->view('draft.edit', $data)
            ->header('Cache-Control', 'no-store')
            ->header('Server-Timing', 'composer;dur='.round((hrtime(true) - $startedAt) / 1_000_000, 1));
    }

    public function validationResponse(Request $request, ValidationException $exception): Response
    {
        $conflict = array_key_exists('draft', $exception->errors());
        $draft = $request->attributes->get('composer.draft');
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => $exception->errors(),
                'version' => ! $conflict && $draft ? $draft->version : null,
            ], 422);
        }
        $errors = (new ViewErrorBag)->put('default', new MessageBag($exception->errors()));
        $response = response()->view('components.validation-errors', compact('errors'), $conflict ? 409 : 422);
        $response->headers->set('HX-Retarget', '#composer-feedback');
        $response->headers->set('HX-Reswap', 'innerHTML');
        // Publication may fail after the draft was saved successfully.
        if (! $conflict && $draft) {
            $response->headers->set('X-Draft-Version', (string) $draft->version);
            if ($draft->exists && ! $draft->thread_id) {
                $response->headers->set('X-Draft-Action', route('draft.update', $draft));
                $response->headers->set('X-Draft-Resume', route('draft.edit', $draft));
            }
        }

        return $response;
    }
}
