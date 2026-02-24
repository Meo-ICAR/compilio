<?php

namespace App\Filament\Resources\AmlQuestionnaires;

use App\Filament\Resources\AmlQuestionnaires\Pages\CreateAmlQuestionnaire;
use App\Filament\Resources\AmlQuestionnaires\Pages\EditAmlQuestionnaire;
use App\Filament\Resources\AmlQuestionnaires\Pages\ListAmlQuestionnaires;
use App\Filament\Resources\AmlQuestionnaires\Schemas\AmlQuestionnaireForm;
use App\Filament\Resources\AmlQuestionnaires\Tables\AmlQuestionnairesTable;
use App\Models\AmlQuestionnaire;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BackedEnum;
use UnitEnum;

class AmlQuestionnaireResource extends Resource
{
    protected static ?string $model = AmlQuestionnaire::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Adeguata verifica AML';

    protected static ?string $modelLabel = 'Adeguata verifica AML';

    protected static ?string $pluralModelLabel = 'Adeguate verifiche AML';

    protected static string|UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AmlQuestionnaireForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AmlQuestionnairesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAmlQuestionnaires::route('/'),
            'create' => CreateAmlQuestionnaire::route('/create'),
            'edit' => EditAmlQuestionnaire::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
