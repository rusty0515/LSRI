<?php

namespace App\Filament\Service\Resources\ServiceRequestResource\Pages;

use App\Filament\Service\Resources\ServiceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceRequest extends CreateRecord
{
    protected static string $resource = ServiceRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
