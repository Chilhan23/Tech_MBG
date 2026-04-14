<?php

namespace App\Filament\Resources\Kelas\Pages;

use App\Filament\Resources\Kelas\KelasResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditKelas extends EditRecord
{
    protected static string $resource = KelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()  ,
            DeleteAction::make(),
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
