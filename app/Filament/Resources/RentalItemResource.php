<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentalItemResource\Pages;
use App\Models\RentalItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentalItemResource extends Resource
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'restore',
            'restore_any',
            'force_delete',
            'force_delete_any',
        ];
    }

    protected static ?string $model = RentalItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'Rental Items';

    protected static ?string $modelLabel = 'Rental Item';

    protected static ?int $navigationSort = 3;

    protected static ?string $pluralModelLabel = 'Rental Items';

    protected static ?string $navigationGroup = 'Menejemen Produk';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ])->orderBy('created_at', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Rental Item')
                    ->columns([
                        'default' => 1,    // ← ADD THIS
                        'sm' => 1,
                        'md' => 2,         // ← 2 columns hanya di desktop
                    ])
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(['default' => 1, 'md' => 2]),  // ← Full width di mobile

                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'perabotan' => 'Perabotan',
                                'ruangan' => 'Ruangan',
                                'voucher' => 'Voucher',
                            ])
                            ->searchable()
                            ->required()
                            ->default('perabotan'),  // ← lowercase

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->inline(false),

                        Forms\Components\TextInput::make('stock')
                            ->label('Stok Rental Item')
                            ->helperText('Stok hanya dapat diisi/ditambah pada menejemen inventori')
                            ->required()
                            ->numeric()
                            ->readOnly()
                            ->default(0),

                        Forms\Components\TextInput::make('cost_price')
                            ->label('Harga modal')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->prefix('Rp')
                            ->rule('min:0'),
                        Forms\Components\TextInput::make('price')
                            ->label('Harga sewa per hari')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->prefix('Rp')
                            ->rule('min:0'),
                    ]),

                Forms\Components\Section::make('Gambar')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Gambar')
                            ->image()
                            ->disk('public')
                            ->directory('rental-items')
                            ->visibility('public')
                            ->imagePreviewHeight('200')
                            ->maxSize(2048)
                            ->helperText('Upload gambar, maksimal 2 MB.')
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public')
                    ->height(48)
                    ->width(48)
                    ->square()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn(?string $state) => match ($state) {
                        'chair' => 'Chair',
                        'table' => 'Table',
                        'tent' => 'Tent',
                        'sound' => 'Sound',
                        default => $state ?? '-',
                    }),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->sortable()
                    ->alignRight()
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format((float) $state, 0, ',', '.')),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->alignRight(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'chair' => 'Chair',
                        'table' => 'Table',
                        'tent' => 'Tent',
                        'sound' => 'Sound',
                        'other' => 'Other',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->withoutGlobalScopes([
    //             SoftDeletingScope::class,
    //         ]);
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentalItems::route('/'),
            'create' => Pages\CreateRentalItem::route('/create'),
            'edit' => Pages\EditRentalItem::route('/{record}/edit'),
        ];
    }
}
