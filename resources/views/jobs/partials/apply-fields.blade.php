@foreach($fieldChunks as $index => $chunk)
    <div class="step {{ $index === 0 ? 'active' : '' }}">
        <div class="step-panel">
            <h3 class="step-title">
                {{ $index === 0 ? 'Basic Details' : ($index === 1 ? 'Professional Information' : 'Final Information') }}
            </h3>

            <p class="step-caption">
                {{ $index === 0 ? 'Start with your personal and contact information.' : ($index === 1 ? 'Add your work background and relevant experience.' : 'Finish the remaining details and upload your required documents.') }}
            </p>

            <div class="fields-grid">
                @foreach($chunk as $field)
                    @php
                        $isWideField = in_array($field->field_type, ['textarea', 'checkbox', 'file']);
                    @endphp

                    @if($field->field_key === 'phone_number')
                        <div class="field full-width">
                            <label>
                                Phone Number
                                @if($field->is_required)
                                    <span class="required-mark">*</span>
                                @endif
                            </label>

                            <div class="inline-row">
                                <div class="code-col">
                                    <select
                                        name="phone_country_code"
                                        class="select"
                                        {{ $field->is_required ? 'required' : '' }}
                                    >
                                        <option value="">Code</option>
                                        @foreach($countryCodes as $code)
                                            <option
                                                value="{{ $code['value'] }}"
                                                @selected(old('phone_country_code') == $code['value'])
                                            >
                                                {{ $code['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="number-col">
                                    <input
                                        type="text"
                                        name="phone_number"
                                        class="input"
                                        placeholder="{{ $field->placeholder ?: 'Enter phone number' }}"
                                        value="{{ old('phone_number') }}"
                                        {{ $field->is_required ? 'required' : '' }}
                                    >
                                </div>
                            </div>

                            @if($field->help_text)
                                <div class="help">{{ $field->help_text }}</div>
                            @endif

                            <div class="error-text">Phone number is required</div>
                        </div>
                        @continue
                    @endif

                    @if($field->field_key === 'whatsapp_number')
                        <div class="field full-width">
                            <label>
                                WhatsApp Number
                                @if($field->is_required)
                                    <span class="required-mark">*</span>
                                @endif
                            </label>

                            <div class="inline-row">
                                <div class="code-col">
                                    <select
                                        name="whatsapp_country_code"
                                        class="select"
                                        {{ $field->is_required ? 'required' : '' }}
                                    >
                                        <option value="">Code</option>
                                        @foreach($countryCodes as $code)
                                            <option
                                                value="{{ $code['value'] }}"
                                                @selected(old('whatsapp_country_code') == $code['value'])
                                            >
                                                {{ $code['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="number-col">
                                    <input
                                        type="text"
                                        name="whatsapp_number"
                                        class="input"
                                        placeholder="{{ $field->placeholder ?: 'Enter WhatsApp number' }}"
                                        value="{{ old('whatsapp_number') }}"
                                        {{ $field->is_required ? 'required' : '' }}
                                    >
                                </div>
                            </div>

                            @if($field->help_text)
                                <div class="help">{{ $field->help_text }}</div>
                            @endif

                            <div class="error-text">WhatsApp number is required</div>
                        </div>
                        @continue
                    @endif

                    <div class="field {{ $isWideField ? 'full-width' : '' }}">
                        <label>
                            {{ $field->label }}
                            @if($field->is_required)
                                <span class="required-mark">*</span>
                            @endif
                        </label>

                        @if($field->field_type === 'text' && $field->field_key !== 'email')
                            <input
                                type="text"
                                name="{{ $field->field_key }}"
                                class="input"
                                placeholder="{{ $field->placeholder }}"
                                value="{{ old($field->field_key) }}"
                                {{ $field->is_required ? 'required' : '' }}
                            >

                        @elseif($field->field_type === 'email' || $field->field_key === 'email')
                            <input
                                type="email"
                                name="{{ $field->field_key }}"
                                class="input"
                                placeholder="{{ $field->placeholder ?: 'Enter your email address' }}"
                                value="{{ old($field->field_key) }}"
                                {{ $field->is_required ? 'required' : '' }}
                            >

                        @elseif($field->field_type === 'number')
                            <input
                                type="number"
                                name="{{ $field->field_key }}"
                                class="input"
                                placeholder="{{ $field->placeholder }}"
                                value="{{ old($field->field_key) }}"
                                {{ $field->is_required ? 'required' : '' }}
                            >

                        @elseif($field->field_type === 'date')
                            <input
                                type="date"
                                name="{{ $field->field_key }}"
                                class="input"
                                value="{{ old($field->field_key) }}"
                                {{ $field->is_required ? 'required' : '' }}
                            >

                        @elseif($field->field_type === 'textarea')
                            <textarea
                                name="{{ $field->field_key }}"
                                class="textarea"
                                placeholder="{{ $field->placeholder }}"
                                {{ $field->is_required ? 'required' : '' }}
                            >{{ old($field->field_key) }}</textarea>

                        @elseif($field->field_type === 'select')
                            <select
                                name="{{ $field->field_key }}"
                                class="select"
                                {{ $field->is_required ? 'required' : '' }}
                            >
                                <option value="">Select</option>
                                @foreach($field->options as $option)
                                    <option
                                        value="{{ $option->option_value }}"
                                        @selected(old($field->field_key) == $option->option_value)
                                    >
                                        {{ $option->option_label }}
                                    </option>
                                @endforeach
                            </select>

                        @elseif($field->field_type === 'checkbox')
                            <div class="checkbox-group">
                                @foreach($field->options as $option)
                                    <label class="checkbox-item">
                                        <input
                                            type="checkbox"
                                            name="{{ $field->field_key }}[]"
                                            value="{{ $option->option_value }}"
                                            @checked(is_array(old($field->field_key)) && in_array($option->option_value, old($field->field_key)))
                                        >
                                        <span>{{ $option->option_label }}</span>
                                    </label>
                                @endforeach
                            </div>

                        @elseif($field->field_type === 'file')
                            <input
                                type="file"
                                name="{{ $field->field_key }}"
                                class="file-input"
                                {{ $field->is_required ? 'required' : '' }}
                            >
                        @endif

                        @if($field->help_text)
                            <div class="help">{{ $field->help_text }}</div>
                        @endif

                        <div class="error-text">This field is required</div>
                    </div>
                @endforeach
            </div>

            <div class="actions-row">
                @if($index > 0)
                    <button type="button" class="btn btn-secondary prevStep">
                        Previous
                    </button>
                @endif

                @if($index < count($fieldChunks) - 1)
                    <button type="button" class="btn btn-primary nextStep">
                        Next
                    </button>
                @else
                    <button type="submit" class="btn btn-submit">
                        Submit Application
                    </button>
                @endif
            </div>
        </div>
    </div>
@endforeach