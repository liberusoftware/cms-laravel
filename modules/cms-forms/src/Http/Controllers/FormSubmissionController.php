<?php

declare(strict_types=1);

namespace Liberu\Cms\Forms\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Contracts\Events\Form\FormSubmitted;
use Liberu\Cms\Forms\Models\Form;
use Liberu\Cms\Forms\Models\FormSubmission;
use Liberu\Cms\Forms\Support\SubmissionValidator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Accepts a public form submission: validates it against the form's schema,
 * stores it (stamped with the form's tenant), and announces FormSubmitted on the
 * event bus. A filled honeypot field is treated as a bot and silently accepted
 * without storing.
 */
final readonly class FormSubmissionController
{
    public function __construct(
        private EventBusInterface $events,
        private SubmissionValidator $validator,
    ) {}

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        $form = Form::query()->where('slug', $slug)->first();

        if (! $form instanceof Form) {
            throw new NotFoundHttpException;
        }

        if ($this->looksAutomated($request)) {
            return response()->json(['message' => $this->successMessage()], 201);
        }

        $data = $this->validator->validate($form, $request->all());

        $submission = FormSubmission::create([
            'form_id' => $form->id,
            'team_id' => $form->team_id,
            'data' => $data,
            'meta' => ['ip' => $request->ip(), 'user_agent' => $request->userAgent()],
        ]);

        $this->events->dispatch(new FormSubmitted($form->slug, $submission->id, $form->team_id, $data));

        return response()->json(['message' => $this->successMessage()], 201);
    }

    private function looksAutomated(Request $request): bool
    {
        $honeypot = config('cms-forms.honeypot');

        return is_string($honeypot) && filled($request->input($honeypot));
    }

    private function successMessage(): string
    {
        $message = config('cms-forms.success_message');

        return is_string($message) ? $message : 'Thank you for your submission.';
    }
}
