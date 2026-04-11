<?php

namespace App\Services;

use App\Models\Employment;
use App\Models\EmploymentDocument;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Language;

class EmploymentDocumentGenerator
{
    public function generateGeneralLetter(Employment $employment, ?User $user = null): EmploymentDocument
    {
        $document = $this->firstOrCreateDocument(
            employment: $employment,
            documentType: 'general_letter',
            title: 'General Letter - ' . ($employment->employee_name ?: 'Employee'),
            user: $user
        );

        $rotation = $employment->currentRotation
            ?: $employment->rotations()->latest('from_date')->first();

        $data = [
            'employment' => $employment,
            'document' => $document,
            'generatedAt' => now(),
            'availability' => $this->resolveAvailability($employment, $rotation),
            'toCompany' => $employment->client_name ?: 'Client',
            'attentionName' => $employment->client_name ?: 'Client Representative',
            'fromCompany' => 'Sada Fezzan for Oil Services',
            'signatoryName' => $employment->operation_officer_name ?: 'Authorized Signatory',
            'positionTitle' => $employment->position_title ?: 'Position',
            'candidateName' => $employment->employee_name ?: 'Candidate',
        ];

        [$docxPath, $pdfPath] = $this->buildGeneralLetterFiles($employment, $document, $data);

        $document->update([
            'title' => 'General Letter - ' . ($employment->employee_name ?: 'Employee'),
            'status' => 'generated',
            'docx_file_path' => $docxPath,
            'pdf_file_path' => $pdfPath,
            'file_path' => $pdfPath,
            'generated_by_user_id' => $user?->id,
            'generated_at' => now(),
            'notes' => 'Auto-generated / regenerated General Letter.',
        ]);

        return $document->fresh();
    }

    public function generateCaf(Employment $employment, ?User $user = null): EmploymentDocument
    {
        $document = $this->firstOrCreateDocument(
            employment: $employment,
            documentType: 'caf',
            title: 'CAF - ' . ($employment->employee_name ?: 'Employee'),
            user: $user
        );

        $data = [
            'employment' => $employment,
            'document' => $document,
            'generatedAt' => now(),
            'locationProject' => $employment->project_name ?: '-',
            'contractNumber' => '-',
            'jobTitle' => $employment->position_title ?: '-',
            'billingRate' => '-',
            'dateRequired' => $employment->currentRotation?->mobilization_date?->format('F d, Y') ?: 'ASAP',
            'requestedByClient' => $employment->client_name ?: '-',
            'requestedBySf' => $employment->operation_officer_name ?: '-',
            'candidateName' => $employment->employee_name ?: '-',
            'nationality' => '-',
            'assignmentType' => 'Temporary',
            'recommendedBy' => $employment->operation_officer_name ?: '-',
        ];

        [$docxPath, $pdfPath] = $this->buildCafFiles($employment, $document, $data);

        $document->update([
            'title' => 'CAF - ' . ($employment->employee_name ?: 'Employee'),
            'status' => 'generated',
            'docx_file_path' => $docxPath,
            'pdf_file_path' => $pdfPath,
            'file_path' => $pdfPath,
            'generated_by_user_id' => $user?->id,
            'generated_at' => now(),
            'notes' => 'Auto-generated / regenerated CAF.',
        ]);

        return $document->fresh();
    }

    protected function firstOrCreateDocument(
        Employment $employment,
        string $documentType,
        string $title,
        ?User $user = null
    ): EmploymentDocument {
        $existing = $employment->documents()
            ->where('document_type', $documentType)
            ->latest('id')
            ->first();

        if ($existing) {
            return $existing;
        }

        return EmploymentDocument::create([
            'employment_id' => $employment->id,
            'document_type' => $documentType,
            'title' => $title,
            'status' => 'draft',
            'generated_by_user_id' => $user?->id,
        ]);
    }

    protected function createBasePhpWord(): PhpWord
    {
        $phpWord = new PhpWord();
        $phpWord->getSettings()->setThemeFontLang(new Language(Language::EN_US));

        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        $phpWord->addTitleStyle(1, [
            'name' => 'Arial',
            'size' => 16,
            'bold' => true,
            'color' => '111827',
        ]);

        $phpWord->addParagraphStyle('p-normal', [
            'spaceAfter' => 180,
            'lineHeight' => 1.15,
        ]);

        $phpWord->addParagraphStyle('p-tight', [
            'spaceAfter' => 80,
            'lineHeight' => 1.05,
        ]);

        $phpWord->addParagraphStyle('p-heading', [
            'spaceBefore' => 180,
            'spaceAfter' => 120,
            'lineHeight' => 1.05,
        ]);

        $phpWord->addFontStyle('f-meta-label', [
            'name' => 'Arial',
            'size' => 11,
            'bold' => true,
            'color' => '111827',
        ]);

        $phpWord->addFontStyle('f-meta-value', [
            'name' => 'Arial',
            'size' => 11,
            'color' => '111827',
        ]);

        $phpWord->addFontStyle('f-ref', [
            'name' => 'Arial',
            'size' => 11,
            'bold' => true,
            'color' => '111827',
        ]);

        $phpWord->addFontStyle('f-table-head', [
            'name' => 'Arial',
            'size' => 10,
            'bold' => true,
            'color' => '111827',
        ]);

        $phpWord->addFontStyle('f-table-cell', [
            'name' => 'Arial',
            'size' => 11,
            'color' => '111827',
        ]);

        $phpWord->addFontStyle('f-body', [
            'name' => 'Arial',
            'size' => 11,
            'color' => '111827',
        ]);

        $phpWord->addFontStyle('f-body-bold', [
            'name' => 'Arial',
            'size' => 11,
            'bold' => true,
            'color' => '111827',
        ]);

        return $phpWord;
    }

    protected function buildGeneralLetterFiles(Employment $employment, EmploymentDocument $document, array $data): array
    {
        $safeReference = Str::slug($document->reference);
        $safeName = Str::slug($employment->employee_name ?: 'employee');

        $baseDir = 'employment-documents/' . $employment->id;
        $docxPath = $baseDir . '/docx/' . $safeReference . '-' . $safeName . '-general-letter.docx';
        $pdfPath = $baseDir . '/pdf/' . $safeReference . '-' . $safeName . '-general-letter.pdf';

        $phpWord = $this->createBasePhpWord();

        $section = $phpWord->addSection([
            'marginTop' => 1700,
            'marginBottom' => 1000,
            'marginLeft' => 900,
            'marginRight' => 900,
        ]);

        $metaTable = $section->addTable([
            'borderSize' => 0,
            'cellMarginLeft' => 0,
            'cellMarginRight' => 0,
            'cellMarginTop' => 40,
            'cellMarginBottom' => 40,
            'width' => 100 * 50,
            'unit' => 'pct',
        ]);

        $metaRows = [
            ['To:', $data['toCompany'], 'f-meta-value'],
            ['Att.:', $data['attentionName'], 'f-meta-value'],
            ['From:', $data['fromCompany'], 'f-meta-value'],
            ['Date:', $data['generatedAt']->format('F d, Y'), 'f-meta-value'],
            ['Ref:', $document->reference, 'f-ref'],
        ];

        foreach ($metaRows as [$label, $value, $valueStyle]) {
            $metaTable->addRow();
            $metaTable->addCell(1100)->addText($label, 'f-meta-label', 'p-tight');
            $metaTable->addCell(8200)->addText($value, $valueStyle, 'p-tight');
        }

        $section->addTextBreak(1);

        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '1F2937',
            'cellMarginTop' => 100,
            'cellMarginBottom' => 100,
            'cellMarginLeft' => 100,
            'cellMarginRight' => 100,
            'width' => 100 * 50,
            'unit' => 'pct',
        ]);

        $table->addRow();
        $table->addCell(2800, ['bgColor' => 'F3F4F6'])->addText('Candidate Name', 'f-table-head', 'p-tight');
        $table->addCell(3800, ['bgColor' => 'F3F4F6'])->addText('Position', 'f-table-head', 'p-tight');
        $table->addCell(2200, ['bgColor' => 'F3F4F6'])->addText('Availability', 'f-table-head', 'p-tight');

        $table->addRow();
        $table->addCell(2800)->addText($data['candidateName'], 'f-table-cell', 'p-tight');
        $table->addCell(3800)->addText($data['positionTitle'], 'f-table-cell', 'p-tight');
        $table->addCell(2200)->addText($data['availability'], 'f-table-cell', 'p-tight');

        $section->addTextBreak(1);
        $section->addText('Dear Sir,', 'f-body-bold', 'p-normal');

        $section->addText(
            'With reference to your request for ' . $data['positionTitle'] . ', please find attached to this letter the CV and CAF for the above-mentioned candidate for your review and approval.',
            'f-body',
            'p-normal'
        );

        $section->addText(
            'Your prompt approval is highly appreciated, as the candidate’s continued availability cannot be guaranteed.',
            'f-body',
            'p-normal'
        );

        $section->addTextBreak(1);
        $section->addText('Sincerely,', 'f-body', 'p-normal');
        $section->addText($data['signatoryName'], 'f-body-bold', 'p-normal');

        $tmpDocx = tempnam(sys_get_temp_dir(), 'gl_docx_');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tmpDocx);
        Storage::disk('public')->put($docxPath, file_get_contents($tmpDocx));
        @unlink($tmpDocx);

        $pdf = Pdf::loadView('employment-documents.general-letter-pdf', $data)
            ->setPaper('a4', 'portrait');

        Storage::disk('public')->put($pdfPath, $pdf->output());

        return [$docxPath, $pdfPath];
    }

    protected function buildCafFiles(Employment $employment, EmploymentDocument $document, array $data): array
    {
        $safeReference = Str::slug($document->reference);
        $safeName = Str::slug($employment->employee_name ?: 'employee');

        $baseDir = 'employment-documents/' . $employment->id;
        $docxPath = $baseDir . '/docx/' . $safeReference . '-' . $safeName . '-caf.docx';
        $pdfPath = $baseDir . '/pdf/' . $safeReference . '-' . $safeName . '-caf.pdf';

        $phpWord = $this->createBasePhpWord();

        $section = $phpWord->addSection([
            'marginTop' => 1700,
            'marginBottom' => 1000,
            'marginLeft' => 900,
            'marginRight' => 900,
        ]);

        $section->addText(
            'CANDIDATE APPROVAL FORM (CAF)',
            ['name' => 'Arial', 'size' => 15, 'bold' => true, 'color' => '111827'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 240]
        );

        $section->addText('A. To Be Completed by Sada Fezzan', 'f-body-bold', 'p-heading');

        $fields = [
            'Location / Project' => $data['locationProject'],
            'Contract Number' => $data['contractNumber'],
            'Job Title' => $data['jobTitle'],
            'Billing Classification / Rate' => $data['billingRate'],
            'Date Required (Effective Date)' => $data['dateRequired'],
            'Requested By Client (Name)' => $data['requestedByClient'],
            'Sada Fezzan (Name)' => $data['requestedBySf'],
            'Candidate Name' => $data['candidateName'],
            'Nationality' => $data['nationality'],
            'Type of Assignment' => $data['assignmentType'],
            'Recommended By' => $data['recommendedBy'],
            'Date' => $data['generatedAt']->format('F d, Y'),
            'Reference' => $document->reference,
        ];

        $fieldsTable = $section->addTable([
            'borderSize' => 0,
            'cellMarginTop' => 60,
            'cellMarginBottom' => 60,
            'width' => 100 * 50,
            'unit' => 'pct',
        ]);

        foreach ($fields as $label => $value) {
            $fieldsTable->addRow();
            $fieldsTable->addCell(3400, ['borderBottomSize' => 6, 'borderBottomColor' => 'D1D5DB'])
                ->addText($label, 'f-meta-label', 'p-tight');
            $fieldsTable->addCell(5600, ['borderBottomSize' => 6, 'borderBottomColor' => 'D1D5DB'])
                ->addText($value ?: '-', $label === 'Reference' ? 'f-ref' : 'f-meta-value', 'p-tight');
        }

        $section->addTextBreak(1);
        $section->addText('B. Approval', 'f-body-bold', 'p-heading');

        $approval = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '1F2937',
            'cellMarginTop' => 100,
            'cellMarginBottom' => 100,
            'cellMarginLeft' => 100,
            'cellMarginRight' => 100,
            'width' => 100 * 50,
            'unit' => 'pct',
        ]);

        $approval->addRow();
        $approval->addCell(3000, ['bgColor' => 'F3F4F6'])->addText('Name', 'f-table-head', 'p-tight');
        $approval->addCell(3000, ['bgColor' => 'F3F4F6'])->addText('Signature', 'f-table-head', 'p-tight');
        $approval->addCell(3000, ['bgColor' => 'F3F4F6'])->addText('Date', 'f-table-head', 'p-tight');

        $approval->addRow(700);
        $approval->addCell(3000)->addText('', 'f-table-cell', 'p-tight');
        $approval->addCell(3000)->addText('', 'f-table-cell', 'p-tight');
        $approval->addCell(3000)->addText('', 'f-table-cell', 'p-tight');

        $section->addTextBreak(1);
        $section->addText('APPROVED', 'f-body-bold', 'p-normal');

        $tmpDocx = tempnam(sys_get_temp_dir(), 'caf_docx_');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tmpDocx);
        Storage::disk('public')->put($docxPath, file_get_contents($tmpDocx));
        @unlink($tmpDocx);

        $pdf = Pdf::loadView('employment-documents.caf-pdf', $data)
            ->setPaper('a4', 'portrait');

        Storage::disk('public')->put($pdfPath, $pdf->output());

        return [$docxPath, $pdfPath];
    }

    protected function resolveAvailability(Employment $employment, $rotation): string
    {
        if ($rotation?->from_date && $rotation->from_date->isFuture()) {
            return $rotation->from_date->format('M j, Y');
        }

        return match ($employment->current_work_status) {
            'pending_mobilization' => 'Pending Mobilization',
            'mobilized', 'on_rotation' => 'Immediately Available',
            'demobilized' => 'Available After Demobilization',
            'on_leave' => 'After Current Leave',
            default => 'As per availability',
        };
    }
}