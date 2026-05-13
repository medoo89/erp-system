@php
    $pendingRequests = $pendingFileRequests ?? collect();
@endphp

@if($pendingRequests->count())
    <section class="sf-pending-requests sf-pending-requests-clean">
        <div class="sf-pending-head">
            <div>
                <div class="sf-pending-kicker">Action Required</div>
                <h2 class="sf-pending-title">Pending File Requests</h2>
                <p class="sf-pending-subtitle">
                    Please upload the requested files. Some documents must be downloaded, signed, and re-uploaded.
                </p>
            </div>
            <a class="sf-pending-open-files" href="{{ route('portal.files.index') }}">Open Files</a>
        </div>

        <div class="sf-pending-body">
            @foreach($pendingRequests as $requestField)
                @php
                    $isSignRequest = (($requestField->request_type ?? null) === 'download_sign_upload') || (bool) ($requestField->signed_file_required ?? false);
                    $categoryLabel = ucfirst(str_replace('_', ' ', (string) ($requestField->document_category ?: 'File')));
                    $sourcePath = $requestField->document_to_sign_path ?? $requestField->source_file_path ?? null;
                @endphp

                <article class="sf-pending-card">
                    <div class="sf-pending-card-top">
                        <div>
                            <div class="sf-pending-label">{{ $requestField->label ?: 'Requested File' }}</div>
                            <div class="sf-pending-help">
                                {{ $categoryLabel }} · {{ $isSignRequest ? 'Download, sign & re-upload' : 'Upload file only' }}
                            </div>
                        </div>

                        <div class="sf-pending-badge">
                            {{ $isSignRequest ? 'Signature Required' : 'Upload Required' }}
                        </div>
                    </div>

                    @if(filled($requestField->instructions ?? null))
                        <div class="sf-pending-help">{{ $requestField->instructions }}</div>
                    @endif

                    @if($isSignRequest && filled($sourcePath))
                        <div class="sf-pending-download-line">
                            <a class="sf-pending-download-btn" href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($sourcePath) }}" target="_blank" rel="noopener">
                                Download file to sign
                            </a>
                            <span>Download it, sign it, then upload the signed copy below.</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('portal.files.upload-requested', ['field' => $requestField->id]) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="sf-pending-form-grid">
                            <div class="sf-pending-field">
                                <label>{{ $isSignRequest ? 'Signed File' : 'File' }}</label>
                                <input class="sf-pending-input" type="file" name="requested_file" required>
                            </div>

                            <div class="sf-pending-field">
                                <label>Document / Issue Date</label>
                                <input class="sf-pending-input" type="date" name="document_date">
                            </div>

                            <div class="sf-pending-field">
                                <label>Expiry Date</label>
                                <input class="sf-pending-input" type="date" name="expiry_date">
                            </div>
                        </div>

                        <textarea class="sf-pending-textarea" name="notes" placeholder="Optional note"></textarea>

                        <button type="submit" class="sf-pending-submit">
                            {{ $isSignRequest ? 'Upload Signed File' : 'Submit Requested File' }}
                        </button>
                    </form>
                </article>
            @endforeach
        </div>
    </section>
@endif
