<?php

namespace App\Filament\Resources\NameResource\Pages;

use App\Filament\Resources\NameResource;
use Filament\Resources\Pages\Page;

class PosRentalPages extends Page
{
    protected static string $resource = NameResource::class;

    protected static string $view = 'filament.resources.name-resource.pages.pos-rental-pages';
}
