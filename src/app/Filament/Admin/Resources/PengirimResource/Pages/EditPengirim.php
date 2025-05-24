<?php

namespace App\Filament\Admin\Resources\PengirimResource\Pages;

use App\Filament\Admin\Resources\PengirimResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengirim extends EditRecord
{
    protected static string $resource = PengirimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
