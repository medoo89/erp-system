<script>
document.addEventListener('DOMContentLoaded', function () {
    const steps = Array.from(document.querySelectorAll('.step'));
    const nextButtons = document.querySelectorAll('.nextStep');
    const prevButtons = document.querySelectorAll('.prevStep');
    const progressFill = document.getElementById('progressFill');
    const stepLabel = document.getElementById('stepLabel');
    const stepPercent = document.getElementById('stepPercent');
    const form = document.getElementById('applicationForm');

    let currentStep = 0;

    function updateProgress() {
        if (!steps.length) return;

        const total = steps.length;
        const current = currentStep + 1;
        const percent = Math.round((current / total) * 100);

        if (progressFill) {
            progressFill.style.width = percent + '%';
        }

        if (stepLabel) {
            stepLabel.textContent = `Step ${current} of ${total}`;
        }

        if (stepPercent) {
            stepPercent.textContent = percent + '%';
        }
    }

    function showStep(index) {
        steps.forEach((step, i) => {
            step.classList.toggle('active', i === index);
        });

        currentStep = index;
        updateProgress();

        const firstField = steps[index]?.querySelector(
            'input:not([type="hidden"]):not([type="checkbox"]):not([type="file"]), select, textarea'
        );

        if (firstField) {
            setTimeout(() => firstField.focus(), 120);
        }

        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        });
    }

    function clearFieldError(fieldWrapper) {
        const input = fieldWrapper.querySelector('.input, .select, .textarea, .file-input');
        const errorText = fieldWrapper.querySelector('.error-text');

        if (input) {
            input.classList.remove('invalid');
        }

        if (errorText) {
            errorText.classList.remove('show');
        }
    }

    function showFieldError(fieldWrapper, message = 'This field is required') {
        const input = fieldWrapper.querySelector('.input, .select, .textarea, .file-input');
        const errorText = fieldWrapper.querySelector('.error-text');

        if (input) {
            input.classList.add('invalid');
        }

        if (errorText) {
            errorText.textContent = message;
            errorText.classList.add('show');
        }
    }

    function validateCheckboxGroup(fieldWrapper) {
        const checkboxes = fieldWrapper.querySelectorAll('input[type="checkbox"]');
        const isRequired = fieldWrapper.querySelector('.required-mark') !== null;

        if (!isRequired) {
            const errorText = fieldWrapper.querySelector('.error-text');
            if (errorText) {
                errorText.classList.remove('show');
            }
            return true;
        }

        const isChecked = Array.from(checkboxes).some(cb => cb.checked);
        const errorText = fieldWrapper.querySelector('.error-text');

        if (!isChecked) {
            if (errorText) {
                errorText.textContent = 'Please select at least one option.';
                errorText.classList.add('show');
            }
            return false;
        }

        if (errorText) {
            errorText.classList.remove('show');
        }

        return true;
    }

    function validatePhoneComposite(fieldWrapper) {
        const codeSelect = fieldWrapper.querySelector('select');
        const numberInput = fieldWrapper.querySelector('input[type="text"]');
        const isRequired = fieldWrapper.querySelector('.required-mark') !== null;

        clearFieldError(fieldWrapper);

        if (!isRequired) {
            return true;
        }

        const code = codeSelect ? (codeSelect.value || '').trim() : '';
        const number = numberInput ? (numberInput.value || '').trim() : '';

        if (!code || !number) {
            showFieldError(fieldWrapper, 'This field is required');
            return false;
        }

        return true;
    }

    function validateField(fieldWrapper) {
        const checkboxGroup = fieldWrapper.querySelector('.checkbox-group');
        if (checkboxGroup) {
            return validateCheckboxGroup(fieldWrapper);
        }

        const inlineRow = fieldWrapper.querySelector('.inline-row');
        if (inlineRow) {
            return validatePhoneComposite(fieldWrapper);
        }

        const input = fieldWrapper.querySelector('.input, .select, .textarea, .file-input');
        if (!input) return true;

        const isRequired = input.hasAttribute('required');

        clearFieldError(fieldWrapper);

        if (!isRequired) {
            return true;
        }

        if (input.type === 'file') {
            if (!input.files || input.files.length === 0) {
                showFieldError(fieldWrapper);
                return false;
            }
            return true;
        }

        if ((input.value ?? '').trim() === '') {
            showFieldError(fieldWrapper);
            return false;
        }

        if (input.type === 'email') {
            const emailValue = input.value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailPattern.test(emailValue)) {
                showFieldError(fieldWrapper, 'Please enter a valid email address.');
                return false;
            }
        }

        return true;
    }

    function validateStep(stepIndex) {
        const step = steps[stepIndex];
        if (!step) return true;

        const fields = Array.from(step.querySelectorAll('.field'));
        let isValid = true;

        fields.forEach(fieldWrapper => {
            const valid = validateField(fieldWrapper);
            if (!valid) {
                isValid = false;
            }
        });

        return isValid;
    }

    function validateAllSteps() {
        let isValid = true;
        let firstInvalidStep = -1;

        steps.forEach((step, index) => {
            const stepValid = validateStep(index);

            if (!stepValid) {
                isValid = false;

                if (firstInvalidStep === -1) {
                    firstInvalidStep = index;
                }
            }
        });

        if (!isValid && firstInvalidStep !== -1) {
            showStep(firstInvalidStep);
        }

        return isValid;
    }

    nextButtons.forEach(button => {
        button.addEventListener('click', function () {
            if (!validateStep(currentStep)) {
                return;
            }

            if (currentStep < steps.length - 1) {
                showStep(currentStep + 1);
            }
        });
    });

    prevButtons.forEach(button => {
        button.addEventListener('click', function () {
            if (currentStep > 0) {
                showStep(currentStep - 1);
            }
        });
    });

    document.querySelectorAll('.input, .select, .textarea, .file-input').forEach(element => {
        element.addEventListener('input', function () {
            const fieldWrapper = element.closest('.field');
            if (fieldWrapper) {
                clearFieldError(fieldWrapper);
            }
        });

        element.addEventListener('change', function () {
            const fieldWrapper = element.closest('.field');
            if (fieldWrapper) {
                clearFieldError(fieldWrapper);
            }
        });
    });

    document.querySelectorAll('.checkbox-group input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const fieldWrapper = checkbox.closest('.field');
            if (!fieldWrapper) return;

            const errorText = fieldWrapper.querySelector('.error-text');
            if (errorText) {
                errorText.classList.remove('show');
            }
        });
    });

    if (form) {
        form.setAttribute('novalidate', 'novalidate');

        form.addEventListener('submit', function (event) {
            if (!validateAllSteps()) {
                event.preventDefault();
                return;
            }
        });
    }

    showStep(0);
});
</script>