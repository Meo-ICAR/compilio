<?php

namespace App\Filament\Resources\RaciAssignements\Schemas;

use App\Models\BusinessFunction;
use App\Models\ProcessTask;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class RaciAssignementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni RACI')
                    ->description('Definisci le responsabilità per questa attività.')
                    ->schema([
                        Select::make('process_task_id')
                            ->label('Attività Processo')
                            ->required()
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return ProcessTask::where('name', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->pluck('name', 'id');
                            })
                            ->getOptionLabelUsing(function ($value) {
                                $task = ProcessTask::find($value);
                                return $task ? $task->name : $value;
                            }),
                        Select::make('business_function_id')
                            ->label('Funzione Business')
                            ->required()
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return BusinessFunction::where('name', 'like', "%{$search}%")
                                    ->orWhere('code', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($function) {
                                        return [$function->id => "{$function->code} - {$function->name}"];
                                    });
                            })
                            ->getOptionLabelUsing(function ($value) {
                                $function = BusinessFunction::find($value);
                                return $function ? "{$function->code} - {$function->name}" : $value;
                            }),
                        Select::make('role')
                            ->label('Ruolo RACI')
                            ->required()
                            ->options([
                                'R' => 'R - Responsible (Responsabile)',
                                'A' => 'A - Accountable (Risponde)',
                                'C' => 'C - Consulted (Consultato)',
                                'I' => 'I - Informed (Informato)',
                            ])
                            ->helperText('Seleziona il ruolo che questa funzione ha nel processo'),
                    ])
                    ->columns(2),
            ]);
    }
}
