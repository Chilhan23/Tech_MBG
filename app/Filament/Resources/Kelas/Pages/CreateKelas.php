<?php

namespace App\Filament\Resources\Kelas\Pages;

use App\Filament\Resources\Kelas\KelasResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateKelas extends CreateRecord
{
    protected static string $resource = KelasResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Buat')
                ->submit('create')
                ->button()
                ->color('info'),
            Action::make('createAnother')
                ->label('Buat & Buat Lagi')
                ->action(function () {
                    $this->create();
                    $this->redirect(route('filament.panitia.resources.kelas.create'));
                })
                ->button()
                ->color('info'),
            $this->getCancelFormAction(),
        ];
    }
}
