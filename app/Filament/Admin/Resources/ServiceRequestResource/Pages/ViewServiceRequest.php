<?php

namespace App\Filament\Admin\Resources\ServiceRequestResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Admin\Resources\ServiceRequestResource;

class ViewServiceRequest extends ViewRecord
{
    protected static string $resource = ServiceRequestResource::class;

    public function getTitle(): string | Htmlable
    {
        /** @var ServiceRequest */
            $record = $this->getRecord();

        return ucwords($record->customer?->name . ': (' . $record->service_number .')');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\ActionGroup::make([
                Actions\DeleteAction::make(),
            ])
        ];
    }

}
