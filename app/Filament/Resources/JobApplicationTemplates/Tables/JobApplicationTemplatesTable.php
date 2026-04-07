<?php

namespace App\Filament\Resources\JobApplicationTemplates\Tables;

use Filament\Actions\DeleteAction; // 🔹 زر حذف
use Filament\Actions\DeleteBulkAction; // 🔹 حذف جماعي
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class JobApplicationTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 🔹 اسم التمبليت
                Tables\Columns\TextColumn::make('name')
                    ->label('Template Name')
                    ->searchable()
                    ->sortable(),

                // 🔹 الوصف
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50),

                // 🔹 هل التمبليت مفعل؟
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                // 🔹 تاريخ الإنشاء
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                // 🔹 تعديل
                EditAction::make(),

                // 🔹 حذف
                DeleteAction::make(),
            ])
            ->toolbarActions([
                // 🔹 حذف جماعي
                DeleteBulkAction::make(),
            ]);
    }
}