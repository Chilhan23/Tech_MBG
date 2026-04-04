<?php

namespace App\Filament\Resources\AbsensiSiswas\Pages;

use App\Filament\Resources\AbsensiSiswas\AbsensiSiswaResource;
use Filament\Resources\Pages\ManageRecords;

class ManageAbsensiSiswas extends ManageRecords
{
    protected static string $resource = AbsensiSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return []; 
    }
}