<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Section;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Siswa')
                ->icon('heroicon-o-plus')
                ->button()
                ->color('info'),
        ];
    }

    public static function getFilterFormActionColumnSpanFull(): bool
    {
        return false;
    }

    protected function configureListDataTable(): void
    {
        parent::configureListDataTable();
    }

    protected function getListTableQuery()
    {
        return parent::getListTableQuery();
    }
}
