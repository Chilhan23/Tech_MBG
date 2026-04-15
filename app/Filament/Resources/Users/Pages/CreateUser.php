<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
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
