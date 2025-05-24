<?php

namespace App\Filament\Admin\Resources\JenisResource\Pages;

use App\Filament\Admin\Resources\JenisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenis extends EditRecord
{
    protected static string $resource = JenisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
