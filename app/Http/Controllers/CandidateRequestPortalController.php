<?php

namespace App\Http\Controllers;

use App\Mail\CandidateRequestSubmissionReceivedMail;
use App\Models\CandidateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CandidateRequestPortalController extends Controller
{
    public function show(string $token)
    {
        $candidateRequest = CandidateRequest::query()
            ->with(['jobApplication.job', 'items'])
            ->where('public_token', $token)
            ->firstOrFail();

        $decodedResponse = json_decode((string) $candidateRequest->candidate_response, true);

        if (! is_array($decodedResponse)) {
            $decodedResponse = [];
        }

        $uploadedFiles = is_array($decodedResponse['uploaded_files'] ?? null)
            ? $decodedResponse['uploaded_files']
            : [];

        $noteResponses = is_array($decodedResponse['note_responses'] ?? null)
            ? $decodedResponse['note_responses']
            : [];

        $thread = is_array($decodedResponse['thread'] ?? null)
            ? $decodedResponse['thread']
            : [];

        if (empty($thread)) {
            $thread[] = [
                'sender' => 'hr',
                'event' => 'request_created',
                'title' => $candidateRequest->title,
                'message' => $candidateRequest->notes,
                'salary' => $candidateRequest->proposed_salary,
                'currency' => $candidateRequest->currency,
                'created_at' => optional($candidateRequest->created_at)?->toDateTimeString(),
            ];
        }

        $candidateMessage = $decodedResponse['message'] ?? null;
        $candidateDecision = $decodedResponse['decision'] ?? null;
        $candidateCounterOffer = $decodedResponse['counter_offer'] ?? null;
        $candidateNegotiationNotes = $decodedResponse['negotiation_notes'] ?? null;

        $uploadedFilesByItem = collect($uploadedFiles)
            ->groupBy('item_id')
            ->toArray();

        $noteResponsesByItem = collect($noteResponses)
            ->keyBy('item_id')
            ->toArray();

        $isClosedPortal = in_array($candidateRequest->request_status, ['accepted', 'declined', 'closed'], true);

        return view('candidate-request.portal', [
            'candidateRequest' => $candidateRequest,
            'uploadedFilesByItem' => $uploadedFilesByItem,
            'noteResponsesByItem' => $noteResponsesByItem,
            'candidateMessage' => $candidateMessage,
            'candidateDecision' => $candidateDecision,
            'candidateCounterOffer' => $candidateCounterOffer,
            'candidateNegotiationNotes' => $candidateNegotiationNotes,
            'thread' => $thread,
            'isClosedPortal' => $isClosedPortal,
        ]);
    }

    public function submit(Request $request, string $token)
    {
        $candidateRequest = CandidateRequest::query()
            ->with(['items', 'jobApplication'])
            ->where('public_token', $token)
            ->firstOrFail();

        if (in_array($candidateRequest->request_status, ['accepted', 'declined', 'closed'], true)) {
            return redirect()
                ->route('candidate-request.show', $candidateRequest->public_token)
                ->with('success', 'This request is already closed and can no longer be updated.');
        }

        $isNegotiation = $candidateRequest->isNegotiation();
        $isFinalOffer = (bool) ($candidateRequest->is_final_offer ?? false);

        $rules = [
            'candidate_response_text' => ['nullable', 'string'],
        ];

        $oldDecoded = json_decode((string) $candidateRequest->candidate_response, true);

        if (! is_array($oldDecoded)) {
            $oldDecoded = [];
        }

        $existingFiles = is_array($oldDecoded['uploaded_files'] ?? null)
            ? $oldDecoded['uploaded_files']
            : [];

        $existingNoteResponses = is_array($oldDecoded['note_responses'] ?? null)
            ? $oldDecoded['note_responses']
            : [];

        $thread = is_array($oldDecoded['thread'] ?? null)
            ? $oldDecoded['thread']
            : [];

        if (empty($thread)) {
            $thread[] = [
                'sender' => 'hr',
                'event' => 'request_created',
                'title' => $candidateRequest->title,
                'message' => $candidateRequest->notes,
                'salary' => $candidateRequest->proposed_salary,
                'currency' => $candidateRequest->currency,
                'created_at' => optional($candidateRequest->created_at)?->toDateTimeString(),
            ];
        }

        if ($isNegotiation) {
            $decisionOptions = $isFinalOffer
                ? ['approved', 'declined']
                : ['approved', 'declined', 'reconsidered'];

            $rules['decision'] = ['required', 'in:' . implode(',', $decisionOptions)];
            $rules['counter_offer'] = ['nullable', 'numeric'];
            $rules['negotiation_notes'] = ['nullable', 'string'];
        }

        foreach ($candidateRequest->items as $item) {
            $field = 'request_item_' . $item->id;

            if (($item->item_type ?? 'file') === 'note') {
                $hasExistingNoteResponse = collect($existingNoteResponses)->contains(function ($row) use ($item) {
                    return (int) ($row['item_id'] ?? 0) === (int) $item->id
                        && filled($row['response'] ?? null);
                });

                $isRequiredNow = $item->is_required && ! $hasExistingNoteResponse;

                $rules[$field] = [$isRequiredNow ? 'required' : 'nullable', 'string'];
                continue;
            }

            $hasExistingUploadedFile = collect($existingFiles)->contains(function ($row) use ($item) {
                return (int) ($row['item_id'] ?? 0) === (int) $item->id
                    && filled($row['stored_path'] ?? null);
            });

            $isRequiredNow = $item->is_required && ! $hasExistingUploadedFile;

            if ($item->allow_multiple) {
                $rules[$field] = [$isRequiredNow ? 'required' : 'nullable', 'array'];
                $rules[$field . '.*'] = ['file', 'max:102400'];
            } else {
                $rules[$field] = [$isRequiredNow ? 'required' : 'nullable', 'file', 'max:102400'];
            }
        }

        $messages = [];

        foreach ($candidateRequest->items as $item) {
            $field = 'request_item_' . $item->id;

            if (($item->item_type ?? 'file') === 'note') {
                $messages[$field . '.required'] = "The response for {$item->label} is required.";
                continue;
            }

            $messages[$field . '.required'] = "The file for {$item->label} is required.";
            $messages[$field . '.file'] = "The selected file for {$item->label} is invalid.";
            $messages[$field . '.max'] = "The file for {$item->label} is too large. Maximum allowed size is 100 MB.";
            $messages[$field . '.*.file'] = "One of the uploaded files for {$item->label} is invalid.";
            $messages[$field . '.*.max'] = "One of the uploaded files for {$item->label} is larger than 100 MB.";
        }

        $validated = $request->validate($rules, $messages);

        if (
            $isNegotiation
            && ($validated['decision'] ?? null) === 'reconsidered'
            && $isFinalOffer
        ) {
            return back()
                ->withErrors([
                    'decision' => 'This is a final offer. Reconsider is no longer available.',
                ])
                ->withInput();
        }

        if (
            $isNegotiation
            && ($validated['decision'] ?? null) === 'reconsidered'
            && blank($validated['counter_offer'] ?? null)
        ) {
            return back()
                ->withErrors([
                    'counter_offer' => 'Please enter your counter offer amount.',
                ])
                ->withInput();
        }

        $newUploadedFiles = [];
        $newNoteResponses = [];

        foreach ($candidateRequest->items as $item) {
            $field = 'request_item_' . $item->id;

            if (($item->item_type ?? 'file') === 'note') {
                $noteValue = $validated[$field] ?? null;

                if (filled($noteValue)) {
                    $newNoteResponses[] = [
                        'item_id' => $item->id,
                        'item_label' => $item->label,
                        'response' => $noteValue,
                    ];
                }

                continue;
            }

            if (! $request->hasFile($field)) {
                continue;
            }

            try {
                if ($item->allow_multiple) {
                    foreach ($request->file($field, []) as $file) {
                        if (! $file || ! $file->isValid()) {
                            return back()
                                ->withErrors([$field => "The request item {$item->id} failed to upload."])
                                ->withInput();
                        }

                        $path = $file->store('candidate-requests', 'public');

                        if (! $path) {
                            return back()
                                ->withErrors([$field => "The request item {$item->id} failed to upload."])
                                ->withInput();
                        }

                        $newUploadedFiles[] = [
                            'item_id' => $item->id,
                            'item_label' => $item->label,
                            'original_name' => $file->getClientOriginalName(),
                            'stored_path' => $path,
                        ];
                    }
                } else {
                    $file = $request->file($field);

                    if ($file) {
                        if (! $file->isValid()) {
                            return back()
                                ->withErrors([$field => "The request item {$item->id} failed to upload."])
                                ->withInput();
                        }

                        $path = $file->store('candidate-requests', 'public');

                        if (! $path) {
                            return back()
                                ->withErrors([$field => "The request item {$item->id} failed to upload."])
                                ->withInput();
                        }

                        $newUploadedFiles[] = [
                            'item_id' => $item->id,
                            'item_label' => $item->label,
                            'original_name' => $file->getClientOriginalName(),
                            'stored_path' => $path,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Candidate request file upload failed', [
                    'candidate_request_id' => $candidateRequest->id,
                    'item_id' => $item->id,
                    'field' => $field,
                    'message' => $e->getMessage(),
                ]);

                return back()
                    ->withErrors([$field => "The request item {$item->id} failed to upload."])
                    ->withInput();
            }
        }

        $mergedNoteResponses = collect($existingNoteResponses)
            ->keyBy('item_id')
            ->merge(collect($newNoteResponses)->keyBy('item_id'))
            ->values()
            ->toArray();

        $responsePayload = [
            'message' => $validated['candidate_response_text'] ?? null,
            'uploaded_files' => array_values(array_merge($existingFiles, $newUploadedFiles)),
            'note_responses' => $mergedNoteResponses,
        ];

        $requestStatus = 'submitted';
        $acceptedSalary = $candidateRequest->accepted_salary;
        $acceptedCurrency = $candidateRequest->accepted_currency;
        $negotiationResult = $candidateRequest->negotiation_result;

        if ($isNegotiation) {
            $decision = $validated['decision'] ?? null;
            $counterOffer = $validated['counter_offer'] ?? null;
            $negotiationNotes = $validated['negotiation_notes'] ?? null;

            $responsePayload['decision'] = $decision;
            $responsePayload['counter_offer'] = $counterOffer;
            $responsePayload['negotiation_notes'] = $negotiationNotes;

            $candidateEvent = match ($decision) {
                'approved' => 'approved',
                'declined' => 'declined',
                'reconsidered' => 'reconsidered',
                default => 'reply',
            };

            $thread[] = [
                'sender' => 'candidate',
                'event' => $candidateEvent,
                'message' => $validated['candidate_response_text'] ?? null,
                'salary' => $decision === 'reconsidered'
                    ? $counterOffer
                    : ($candidateRequest->proposed_salary ?? null),
                'currency' => $candidateRequest->currency,
                'notes' => $negotiationNotes,
                'note_responses' => collect($mergedNoteResponses)
                    ->mapWithKeys(fn ($item) => [
                        (string) ($item['item_label'] ?? 'Note') => (string) ($item['response'] ?? ''),
                    ])
                    ->toArray(),
                'created_at' => now()->toDateTimeString(),
            ];

            $requestStatus = match ($decision) {
                'approved' => 'accepted',
                'declined' => 'declined',
                'reconsidered' => 'reconsidered',
                default => 'submitted',
            };

            if ($decision === 'approved') {
                $acceptedSalary = $candidateRequest->proposed_salary;
                $acceptedCurrency = $candidateRequest->currency;
                $negotiationResult = 'accepted';
            } elseif ($decision === 'declined') {
                $negotiationResult = 'declined';
            } elseif ($decision === 'reconsidered') {
                $negotiationResult = 'reconsidered';
            }
        } else {
            $thread[] = [
                'sender' => 'candidate',
                'event' => 'reply',
                'message' => $validated['candidate_response_text'] ?? null,
                'note_responses' => collect($mergedNoteResponses)
                    ->mapWithKeys(fn ($item) => [
                        (string) ($item['item_label'] ?? 'Note') => (string) ($item['response'] ?? ''),
                    ])
                    ->toArray(),
                'created_at' => now()->toDateTimeString(),
            ];
        }

        $responsePayload['thread'] = $thread;

        $candidateRequest->update([
            'candidate_response' => json_encode($responsePayload, JSON_UNESCAPED_UNICODE),
            'candidate_counter_offer' => $validated['counter_offer'] ?? null,
            'request_status' => $requestStatus,
            'responded_at' => now(),
            'accepted_salary' => $acceptedSalary,
            'accepted_currency' => $acceptedCurrency,
            'negotiation_result' => $negotiationResult,
        ]);

        $hasFiles = count($newUploadedFiles) > 0 || count($existingFiles) > 0;
        $hasNotes = count($mergedNoteResponses) > 0;
        $hasMessage = filled($validated['candidate_response_text'] ?? null);

        $candidateRequestStatus = 'response_received';

        if ($isNegotiation) {
            if (($validated['decision'] ?? null) === 'approved') {
                $candidateRequestStatus = 'request_completed';
            } elseif (($validated['decision'] ?? null) === 'declined') {
                $candidateRequestStatus = 'request_completed';
            } else {
                $candidateRequestStatus = 'response_received';
            }
        } elseif ($hasFiles && $this->allRequiredItemsCompleted($candidateRequest, $responsePayload)) {
            $candidateRequestStatus = 'request_completed';
        } elseif ($hasFiles) {
            $candidateRequestStatus = 'documents_submitted';
        } elseif ($hasNotes || $hasMessage) {
            $candidateRequestStatus = 'response_received';
        }

        $candidateRequest->jobApplication?->update([
            'candidate_request_status' => $candidateRequestStatus,
        ]);

        if (filled($candidateRequest->jobApplication?->email)) {
            try {
                $portalUrl = rtrim(config('app.public_app_url') ?: config('app.url'), '/') . '/candidate-request/' . $candidateRequest->public_token;

                Mail::to($candidateRequest->jobApplication->email)->send(
                    new CandidateRequestSubmissionReceivedMail($candidateRequest->fresh(), $portalUrl)
                );
            } catch (\Throwable $e) {
                Log::error('Candidate request submission confirmation email failed', [
                    'candidate_request_id' => $candidateRequest->id,
                    'job_application_id' => $candidateRequest->job_application_id,
                    'email' => $candidateRequest->jobApplication?->email,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('candidate-request.show', $candidateRequest->public_token)
            ->with('success', in_array($candidateRequest->fresh()->request_status, ['accepted', 'declined'], true)
                ? 'Your response has been submitted and this request is now closed.'
                : 'Your response has been submitted successfully.');
    }

    protected function allRequiredItemsCompleted(CandidateRequest $candidateRequest, array $responsePayload): bool
    {
        $uploadedFiles = collect($responsePayload['uploaded_files'] ?? []);
        $noteResponses = collect($responsePayload['note_responses'] ?? []);

        foreach ($candidateRequest->items as $item) {
            if (! $item->is_required) {
                continue;
            }

            if (($item->item_type ?? 'file') === 'note') {
                $hasNote = $noteResponses->contains(function ($row) use ($item) {
                    return (int) ($row['item_id'] ?? 0) === (int) $item->id
                        && filled($row['response'] ?? null);
                });

                if (! $hasNote) {
                    return false;
                }

                continue;
            }

            $hasFile = $uploadedFiles->contains(function ($row) use ($item) {
                return (int) ($row['item_id'] ?? 0) === (int) $item->id
                    && filled($row['stored_path'] ?? null);
            });

            if (! $hasFile) {
                return false;
            }
        }

        return true;
    }
}