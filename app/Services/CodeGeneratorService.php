<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Employment;
use App\Models\PreEmployment;
use App\Models\Project;
use Illuminate\Support\Str;

class CodeGeneratorService
{
    public function generateClientCode(string $name, ?int $ignoreId = null): string
    {
        $base = $this->buildAlphaCode($name, 2, 4, 'CL');

        $code = $base;
        $counter = 2;

        while ($this->clientCodeExists($code, $ignoreId)) {
            $code = $base . $counter;
            $counter++;
        }

        return $code;
    }

    public function generateProjectCode(string $name, ?int $clientId = null, ?int $ignoreId = null): string
    {
        $base = $this->buildAlphaCode($name, 2, 4, 'PR');

        $code = $base;
        $counter = 2;

        while ($this->projectCodeExists($code, $clientId, $ignoreId)) {
            $code = $base . $counter;
            $counter++;
        }

        return $code;
    }

    public function generateEmployeeCode(string $clientCode, string $projectCode): string
    {
        $prefix = 'SF-' . strtoupper($clientCode) . '-' . strtoupper($projectCode) . '-';

        $lastEmploymentCode = Employment::query()
            ->where('employee_code', 'like', $prefix . '%')
            ->orderByDesc('employee_code')
            ->value('employee_code');

        $lastPreEmploymentCode = PreEmployment::query()
            ->where('employee_code', 'like', $prefix . '%')
            ->orderByDesc('employee_code')
            ->value('employee_code');

        $lastSequence = 0;

        foreach ([$lastEmploymentCode, $lastPreEmploymentCode] as $code) {
            if ($code && preg_match('/(\d+)$/', $code, $matches)) {
                $lastSequence = max($lastSequence, (int) $matches[1]);
            }
        }

        $nextSequence = str_pad((string) ($lastSequence + 1), 3, '0', STR_PAD_LEFT);

        return $prefix . $nextSequence;
    }

    protected function buildAlphaCode(string $name, int $min = 2, int $max = 4, string $fallback = 'XX'): string
    {
        $name = trim($name);

        if ($name === '') {
            return $fallback;
        }

        $words = preg_split('/[\s\-_\/]+/', $name, -1, PREG_SPLIT_NO_EMPTY);
        $words = array_values(array_filter($words, fn ($word) => preg_match('/[A-Za-z]/', $word)));

        if (count($words) >= 2) {
            $letters = '';

            foreach ($words as $word) {
                $clean = preg_replace('/[^A-Za-z]/', '', $word);

                if ($clean !== '') {
                    $letters .= strtoupper(Str::substr($clean, 0, 1));
                }

                if (strlen($letters) >= $max) {
                    break;
                }
            }

            if (strlen($letters) >= $min) {
                return substr($letters, 0, $max);
            }
        }

        $clean = strtoupper(preg_replace('/[^A-Za-z]/', '', $name));

        if ($clean === '') {
            return $fallback;
        }

        return substr(str_pad($clean, $min, 'X'), 0, $max);
    }

    protected function clientCodeExists(string $code, ?int $ignoreId = null): bool
    {
        return Client::query()
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->where('code', $code)
            ->exists();
    }

    protected function projectCodeExists(string $code, ?int $clientId = null, ?int $ignoreId = null): bool
    {
        return Project::query()
            ->when($clientId, fn ($q) => $q->where('client_id', $clientId))
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->where('project_code', $code)
            ->exists();
    }
}