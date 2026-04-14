<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

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
                    $this->redirect(route('filament.panitia.resources.students.create'));
                })
                ->button()
                ->color('info'),
            $this->getCancelFormAction(),
        ];
    }
}
