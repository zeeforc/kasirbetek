<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\StockReport;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\StockReportResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class StockReportResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'create',
            'update',
            'delete_any',
        ];
    }

    protected static ?string $model = StockReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?string $navigationLabel = 'Laporan Stok';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationGroup = 'Menejemen keuangan';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Setting Laporan Stok')
                    ->schema([
                        Forms\Components\DatePicker::make('report_date')
                            ->label('Tanggal Laporan')
                            ->required()
                            ->default(today()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama/Kode Laporan')
                    ->weight('semibold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('report_date')
                    ->label('Tanggal Laporan')
                    ->date('d F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_items')
                    ->label('Jumlah Produk')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_cost_value')
                    ->label('Total Nilai Modal')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_selling_value')
                    ->label('Total Nilai Jual')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('primary')
                    ->url(fn($record) => route('stock-reports.download', $record))
                    ->openUrlInNewTab(true),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockReports::route('/'),
            'create' => Pages\CreateStockReport::route('/create'),
            'edit' => Pages\EditStockReport::route('/{record}/edit'),
        ];
    }
}
