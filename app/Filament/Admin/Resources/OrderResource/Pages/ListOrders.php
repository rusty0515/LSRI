<?php

namespace App\Filament\Admin\Resources\OrderResource\Pages;

use App\Filament\Admin\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-o-plus')->label('New Order'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make()
                    ->label('All'),

            'Pending' => Tab::make()
                    ->label('Pending')
                    ->modifyQueryUsing(function ($query) {
                        return $query->where('payment_status', 'pending');
                    }),

            'Paid' => Tab::make()
                    ->label('Paid')
                    ->modifyQueryUsing(function ($query) {
                        return $query->where('payment_status', 'paid');
                    }),

            'Failed' => Tab::make()
                    ->label('Failed')
                    ->modifyQueryUsing(function ($query) {
                        return $query->where('payment_status', 'failed');
                    })


                   
        ];
    }
}
