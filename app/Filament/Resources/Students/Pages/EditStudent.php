<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;


class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Lihat Data')
                ->icon('heroicon-o-eye'),
            DeleteAction::make()->label('Hapus Data')
                ->icon('heroicon-o-trash'),
            Action::make('back')
                ->label('Kembali')
                ->url(static::getResource()::getUrl('index')) // Kembali ke daftar resource
                ->button()
                ->color('gray')
                ->icon('heroicon-o-chevron-left'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save')
                ->button()
                ->color('info'),
            $this->getCancelFormAction(),
        ];
    }
}
