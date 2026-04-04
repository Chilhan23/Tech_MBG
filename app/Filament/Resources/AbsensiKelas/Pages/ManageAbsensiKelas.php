<?php

namespace App\Filament\Resources\AbsensiKelas\Pages;

use App\Filament\Resources\AbsensiKelas\AbsensiKelasResource;
use Filament\Resources\Pages\ManageRecords;

class ManageAbsensiKelas extends ManageRecords
{
    protected static string $resource = AbsensiKelasResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}