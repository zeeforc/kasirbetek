<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;

class PosRentalPage extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Halaman Rental';

    protected static string $view = 'filament.pages.pos-rental-page';

    protected static ?string $slug = 'pos-rental';

    protected static ?string $title = 'Halaman Rental';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }
}
