<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Ubah Data')
                ->icon('heroicon-o-pencil'),
            Action::make('back')
                ->label('Kembali')
                ->url(static::getResource()::getUrl('index')) // Kembali ke daftar resource
                ->button()
                ->color('gray')
                ->icon('heroicon-o-chevron-left'),
        ];
    }
}
