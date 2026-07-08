<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MealAttendanceResource\Pages;
use App\Filament\Resources\MealAttendanceResource\RelationManagers;
use App\Models\MealAttendance;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MealAttendanceResource extends Resource
{
    protected static ?string $model = MealAttendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Alimentación';
    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Ingreso de alimentación';
    protected static ?string $modelLabel = 'Ingreso de alimentación';
    protected static ?string $pluralModelLabel = 'Ingreso de alimentación';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->default(auth()->id())
                    ->hidden(fn() => !auth()->user()->hasRole('Administrador'))
                    ->required(),

                Select::make('week_start')
                    ->label('Semana')
                    ->hiddenOn('edit')
                    ->options(function () {
                        $weeks = [];

                        $start = now()->subMonths(1)->startOfWeek(\Carbon\Carbon::MONDAY);
                        $end = now()->addMonths(2)->startOfWeek(\Carbon\Carbon::MONDAY);

                        while ($start->lte($end)) {
                            $monday = $start->copy();
                            $friday = $start->copy()->addDays(4);

                            $weeks[$monday->toDateString()] = 'Semana del '
                                . $monday->format('d/m/Y')
                                . ' al '
                                . $friday->format('d/m/Y');

                            $start->addWeek();
                        }

                        return $weeks;
                    })
                    ->default(now()->startOfWeek(\Carbon\Carbon::MONDAY)->addWeek()->toDateString())
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),

                Toggle::make('breakfast')
                    ->label('Desayuno')
                    ->columnSpan(2)
                    ->onColor('success')
                    ->offColor('danger'),

                Toggle::make('lunch')
                    ->label('Almuerzo')
                    ->columnSpan(2)
                    ->onColor('success')
                    ->offColor('danger'),

                Toggle::make('dinner')
                    ->label('Cena')
                    ->columnSpan(2)
                    ->onColor('success')
                    ->offColor('danger'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('week_start')
                    ->label('Semana')
                    ->formatStateUsing(
                        fn($state) =>
                        'Semana del ' .
                        \Carbon\Carbon::parse($state)->format('d/m/Y') .
                        ' al ' .
                        \Carbon\Carbon::parse($state)->addDays(4)->format('d/m/Y')
                    )
                    ->sortable(),

                IconColumn::make('breakfast')
                    ->label('Desayuno')
                    ->boolean(),

                IconColumn::make('lunch')
                    ->label('Almuerzo')
                    ->boolean(),

                IconColumn::make('dinner')
                    ->label('Cena')
                    ->boolean(),
            ])
            ->actions([
                DeleteAction::make('Eliminar')->before(function (MealAttendance $record) {
                    $weekStart = Carbon::parse($record->week_start);

                    $limitDate = $weekStart
                        ->copy()
                        ->subDays(5)
                        ->setTime(15, 0, 0);

                    if (now()->greaterThan($limitDate)) {
                        Notification::make()
                            ->title('Error')
                            ->body('El plazo para modificar comidas de esa semana finalizó el miércoles anterior a las 15:00 p.m.')
                            ->danger()
                            ->send();

                        $this->halt();
                    }
                })
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->hidden(fn() => !auth()->user()->hasRole('Administrador')),

                Filter::make('date')
                    ->label('Filtrar por fecha')
                    ->form([
                        DatePicker::make('from')
                            ->label('Desde'),

                        DatePicker::make('until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('date', 'desc');
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
            'index' => Pages\ListMealAttendances::route('/'),
            'create' => Pages\CreateMealAttendance::route('/create'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {

        if (auth()->user()->hasRole('Administrador')) {
            return parent::getEloquentQuery();
        }
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}
