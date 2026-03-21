<?php

namespace App\Filament\Resources\ProcessTasks\Schemas;

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

class ProcessTaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Definizione Attività')
                    ->description("Collega il task al prodotto e definisci l'ordine di esecuzione.")
                    ->schema([
                        TextInput::make('process.name')
                            ->label('Processo Padre')
                            ->disabled()
                            ->helperText('Processo padre del task'),
                        TextInput::make('name')
                            ->label('Nome Task')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('groupcode')
                            ->label('Codice Task')
                            ->nullable()
                            ->maxLength(50)
                            ->helperText('Codice identificativo del task (es. OAM-01)'),
                        TextInput::make('code')
                            ->label('Codice Dettaglio')
                            ->nullable()
                            ->maxLength(50)
                            ->helperText('Codice specifico per checklist items (es. RICEZIONE-PEC)'),
                        TextInput::make('sort_order')
                            ->label('Ordine Sequenza')
                            ->numeric()
                            ->default(0),
                        Select::make('taskable_type')
                            ->label('Tipo Entità')
                            ->options([
                                'App\Models\PracticeScope' => 'Ambito Pratica (Prodotto)',
                                'App\Models\Company' => 'Azienda',
                                'App\Models\Client' => 'Cliente',
                                'App\Models\Principal' => 'Mandante',
                                'App\Models\Agent' => 'Agente',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn($state, callable $set) => $set('taskable_id', null)),
                        Select::make('taskable_id')
                            ->label('Entità Selezionata')
                            ->required()
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search, callable $get) {
                                $type = $get('taskable_type');
                                if (!$type)
                                    return [];

                                $model = new $type;
                                return $model::where('name', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->pluck('name', 'id');
                            })
                            ->getOptionLabelUsing(function ($value, callable $get) {
                                $type = $get('taskable_type');
                                if (!$type || !$value)
                                    return '';

                                $model = new $type;
                                $record = $model::find($value);
                                return $record ? $record->name : $value;
                            }),
                    ]),
                Section::make('Matrice RACI')
                    ->description('Assegna le responsabilità alle funzioni business.')
                    ->schema([
                        Repeater::make('raciAssignments')
                            ->relationship()  // Laravel 12 + Filament 5.2 gestiscono la pivot automaticamente
                            ->schema([
                                Select::make('role')
                                    ->label('Ruolo')
                                    ->options([
                                        'R' => 'Responsible (Chi esegue)',
                                        'A' => 'Accountable (Chi approva)',
                                        'C' => 'Consulted (Chi supporta)',
                                        'I' => 'Informed (Chi osserva)',
                                    ])
                                    ->required()
                                    ->native(false),
                                Select::make('business_function_id')
                                    ->label('Funzione Business')
                                    ->relationship('businessFunction', 'name')
                                    ->required()
                                    ->distinct()  // Impedisce di selezionare la stessa funzione due volte nel repeater
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            ])
                            ->columns(2)
                            ->itemLabel(fn(array $state): ?string =>
                                $state['role'] ?? 'Nuova assegnazione')
                            ->collapsible()
                            ->cloneable()  // Feature comoda per duplicare ruoli velocemente
                    ]),
            ]);
    }
}
