<?php

namespace App\Exceptions;

use App\Http\Middleware\RenderPostComposer;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (ValidationException $exception, Request $request) {
            if (($request->header('HX-Request') === 'true' || $request->expectsJson())
                && $request->routeIs('draft.reply.update', 'draft.topic.update', 'draft.update')) {
                return app(RenderPostComposer::class)->validationResponse($request, $exception);
            }
            $draft = $request->attributes->get('composer.draft');
            if ($draft?->exists && ! $draft->thread_id && ! array_key_exists('draft', $exception->errors())) {
                $exception->redirectTo(route('draft.edit', $draft));
            }
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
