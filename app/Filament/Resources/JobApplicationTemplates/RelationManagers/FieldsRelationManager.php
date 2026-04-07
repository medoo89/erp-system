<?php

namespace App\Filament\Resources\JobApplicationTemplates\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FieldsRelationManager extends RelationManager
{
    // 🔹 اسم العلاقة الموجودة داخل Model القالب
    protected static string $relationship = 'fields';

    // 🔹 عنوان القسم داخل صفحة التمبليت
    protected static ?string $title = 'Template Fields';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // 🔹 اسم الحقل الظاهر
                Tables\Columns\TextColumn::make('label')
                    ->label('Field Label')
                    ->searchable(),

                // 🔹 المفتاح الداخلي للحقل
                Tables\Columns\TextColumn::make('field_key')
                    ->label('Field Key'),

                // 🔹 نوع الحقل
                Tables\Columns\TextColumn::make('field_type')
                    ->label('Field Type')
                    ->badge(),

                // 🔹 الترتيب داخل التمبليت
                Tables\Columns\TextColumn::make('pivot.sort_order')
                    ->label('Order'),
            ])
            ->headerActions([
                AttachAction::make()
                    // 🔹 نخلي النافذة تعرض اسم الحقل من عمود label
                    ->recordTitleAttribute('label')

                    // 🔹 تحميل الخيارات مسبقًا
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                // 🔹 إزالة الحقل من التمبليت
                DetachAction::make(),
            ]);
    }
}