<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobApplicationField;

class DefaultApplicantFieldsSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [

            // 🔹 1 Full Name
            [
                'label' => 'Full Name',
                'field_key' => 'full_name',
                'field_type' => 'text',
                'field_group' => 'basic',
                'placeholder' => 'Enter your full name',
                'help_text' => 'Please enter your name exactly as in passport.',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'is_global' => true,
                'job_id' => null,
            ],

            // 🔹 2 Phone Number
            [
                'label' => 'Phone Number',
                'field_key' => 'phone_number',
                'field_type' => 'text',
                'field_group' => 'basic',
                'placeholder' => 'Enter your phone number',
                'help_text' => null,
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'is_global' => true,
                'job_id' => null,
            ],

            // 🔹 3 WhatsApp Number
            [
                'label' => 'WhatsApp Number',
                'field_key' => 'whatsapp_number',
                'field_type' => 'text',
                'field_group' => 'basic',
                'placeholder' => 'Enter your WhatsApp number',
                'help_text' => null,
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'is_global' => true,
                'job_id' => null,
            ],

            // 🔹 4 Email Address
            [
                'label' => 'Email Address',
                'field_key' => 'email',
                'field_type' => 'email',
                'field_group' => 'basic',
                'placeholder' => 'Enter your email address',
                'help_text' => 'Use a valid email address.',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
                'is_global' => true,
                'job_id' => null,
            ],

            // 🔹 5 Nationality
            [
                'label' => 'Nationality',
                'field_key' => 'nationality',
                'field_type' => 'select',
                'field_group' => 'basic',
                'placeholder' => null,
                'help_text' => 'Select your nationality.',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
                'is_global' => true,
                'job_id' => null,
            ],

            // 🔹 6 Current Location
            [
                'label' => 'Current Location',
                'field_key' => 'current_location',
                'field_type' => 'text',
                'field_group' => 'basic',
                'placeholder' => 'Enter your current location',
                'help_text' => null,
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 6,
                'is_global' => true,
                'job_id' => null,
            ],

            // 🔹 7 Years of Experience
            [
                'label' => 'Years of Experience',
                'field_key' => 'experience',
                'field_type' => 'text',
                'field_group' => 'basic',
                'placeholder' => 'Enter your years of experience',
                'help_text' => null,
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 7,
                'is_global' => true,
                'job_id' => null,
            ],

            // 🔹 8 Current Job Title
            [
                'label' => 'Current Job Title',
                'field_key' => 'job_title',
                'field_type' => 'text',
                'field_group' => 'basic',
                'placeholder' => 'Enter your current job title',
                'help_text' => null,
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 8,
                'is_global' => true,
                'job_id' => null,
            ],

            // 🔹 9 Notice Period / Availability
            [
                'label' => 'Notice Period / Availability',
                'field_key' => 'availability',
                'field_type' => 'text',
                'field_group' => 'basic',
                'placeholder' => 'Enter your availability',
                'help_text' => null,
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 9,
                'is_global' => true,
                'job_id' => null,
            ],

            // 🔹 10 Brief Summary of Experience
            [
                'label' => 'Brief Summary of Experience',
                'field_key' => 'summary',
                'field_type' => 'textarea',
                'field_group' => 'basic',
                'placeholder' => 'Write a brief summary of your experience',
                'help_text' => null,
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 10,
                'is_global' => true,
                'job_id' => null,
            ],

            // 🔹 11 Upload Your Certificates (Optional)
            [
                'label' => 'Upload Your Certificates (if Any)',
                'field_key' => 'certificates',
                'field_type' => 'file',
                'field_group' => 'additional',
                'placeholder' => null,
                'help_text' => 'Optional: upload certificates if available.',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 11,
                'is_global' => true,
                'job_id' => null,
            ],

            // 🔹 12 Upload CV
            [
                'label' => 'Upload Your CV',
                'field_key' => 'cv_file',
                'field_type' => 'file',
                'field_group' => 'basic',
                'placeholder' => null,
                'help_text' => 'Upload your updated CV.',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 12,
                'is_global' => true,
                'job_id' => null,
            ],
        ];

        foreach ($fields as $field) {
            JobApplicationField::updateOrCreate(
                ['field_key' => $field['field_key']],
                $field
            );
        }

        // 🔹 نخفي الحقول القديمة اللي معاش نبوها في التمبليت
        JobApplicationField::whereIn('field_key', [
            'phone',
            'phone_country_code',
            'whatsapp_country_code',
        ])->update([
            'is_active' => false,
        ]);
    }
}