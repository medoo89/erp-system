<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobApplicationExportController extends Controller
{
    public function exportCsv(): StreamedResponse
    {
        $fileName = 'job_applications.csv';

        $applications = JobApplication::with(['job', 'values.field'])->latest()->get();

        $response = new StreamedResponse(function () use ($applications) {
            $handle = fopen('php://output', 'w');

            // الأعمدة الأساسية
            $baseHeaders = [
                'ID',
                'Full Name',
                'Email',
                'Phone',
                'Job Title',
                'Status',
                'CV Link',
                'Applied At',
            ];

            // الحقول التي لا نريد تكرارها من الـ dynamic fields
            $excludedFieldKeys = [
                'full_name',
                'email',
                'phone',
                'cv_file',
                'cover_letter',
            ];

            $dynamicFieldLabels = [];
            $dynamicFieldIds = [];

            foreach ($applications as $application) {
                foreach ($application->values as $value) {
                    if ($value->field && !in_array($value->field->field_key, $excludedFieldKeys)) {
                        $dynamicFieldIds[$value->field->id] = $value->field->id;
                        $dynamicFieldLabels[$value->field->id] = $value->field->label;
                    }
                }
            }

            fputcsv($handle, array_merge($baseHeaders, array_values($dynamicFieldLabels)));

            foreach ($applications as $application) {
                $cvLink = $application->cv_path ? asset('storage/' . $application->cv_path) : '';

                $row = [
                    $application->id,
                    $application->full_name,
                    $application->email,
                    $application->phone,
                    $application->job?->title,
                    $application->status,
                    $cvLink,
                    optional($application->created_at)->toDateTimeString(),
                ];

                $dynamicValues = [];

                foreach (array_keys($dynamicFieldIds) as $fieldId) {
                    $stored = $application->values->firstWhere('field_id', $fieldId);
                    $dynamicValues[] = $stored?->value ?? '';
                }

                fputcsv($handle, array_merge($row, $dynamicValues));
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }
}