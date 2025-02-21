<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Leave;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LeaveResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\LeaveResource\RelationManagers;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-start-on-rectangle';
    protected static ?int $navigationSort = 9;
    protected static ?string $navigationGroup = 'Attendance Management';

    public static function getNavigationBadge(): ?string
    {
        
        if(!Auth::user()->hasRole('super_admin')){
            return static::getModel()::where('status', 'Pending')->where('user_id', Auth::user()->id)->count();
        }else{
            return static::getModel()::where('status', 'Pending')->count();
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if(!Auth::user()->hasRole('super_admin')){
            return 'warning';
        }else{
            return static::getModel()::where('status', 'Pending')->count() > 5 ? 'danger' : 'warning';
        }
    }

    public static function form(Form $form): Form
    {
        $schema = [
            Forms\Components\Section::make('Detail')
                ->schema([
                    Forms\Components\DatePicker::make('start_date')
                        ->required()
                        ->prefix('Starts')
                        ->displayFormat('d F Y')
                        ->formatStateUsing(fn() => today()->toDateString())
                        ->minDate(now()->toDateString())
                        ->native(false),
                    Forms\Components\DatePicker::make('end_date')
                        ->required()
                        ->minDate(now()->toDateString())
                        ->prefix('Ends')
                        ->displayFormat('d F Y')
                        ->native(false),
                    Forms\Components\Textarea::make('reason')
                        ->required()
                        ->columnSpanFull(),
                ])->columns(2)
        ];

        if (Auth::user()->hasRole('super_admin')) {
            $schema[] = Forms\Components\Section::make('Approval')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'Pending' => 'Pending',
                            'Approved' => 'Approved',
                            'Rejected' => 'Rejected',
                        ])
                        ->native(false)
                        ->required(),
                    Forms\Components\Textarea::make('note')
                        ->columnSpanFull(),
                ]);
        }
        return $form
            ->schema($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $is_super_admin = Auth::user()->hasRole('super_admin');

                if (!$is_super_admin) {
                    $query->where('user_id', Auth::user()->id);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }
}
