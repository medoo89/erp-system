<?php

namespace App\Filament\Resources\Jobs\Schemas;

use App\Models\JobApplicationTemplate;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class JobForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Job Details')
                    ->schema([

                        // 🔹 اسم الوظيفة
                        Forms\Components\TextInput::make('title')
                            ->label('Job Title')
                            ->required()
                            ->maxLength(255),

                        // 🔹 القسم
                        Forms\Components\TextInput::make('department')
                            ->label('Department')
                            ->maxLength(255),

                        // 🔹 الموقع
                        Forms\Components\TextInput::make('location')
                            ->label('Location')
                            ->maxLength(255),

                        // 🔹 نوع التوظيف
                        Forms\Components\Select::make('employment_type')
                            ->label('Employment Type')
                            ->options([
                                'full_time' => 'Full Time',
                                'part_time' => 'Part Time',
                                'rotation' => 'Rotation',
                                'contract' => 'Contract',
                            ])
                            ->required(),

                        // 🔹 القالب المرتبط بالوظيفة
                        // 🔹 من هنا نختار Template جاهز للفورم
                        Forms\Components\Select::make('template_id')
                            ->label('Application Template')
                            ->options(
                                JobApplicationTemplate::query()
                                    ->where('is_active', true)
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        // 🔹 هل الوظيفة منشورة/مفعلة
                        Forms\Components\Toggle::make('is_active')
                            ->label('Published')
                            ->default(true),

                        // 🔹 تاريخ إغلاق التقديم
                        Forms\Components\DatePicker::make('closing_date')
                            ->label('Expiry Date')
                            ->native(false),

                        // 🔹 وصف الوظيفة
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(5)
                            ->columnSpanFull(),

                        // 🔹 متطلبات الوظيفة
                        Forms\Components\Textarea::make('requirements')
                            ->label('Requirements')
                            ->rows(5)
                            ->columnSpanFull(),

                    ])
                    ->columns(2),
            ]);
    }
}