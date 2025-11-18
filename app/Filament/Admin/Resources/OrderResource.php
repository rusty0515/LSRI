<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Forms\Components\AddressForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Split;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\OrderResource\Pages;
use Filament\Infolists\Components\Section as InfoSec;
use App\Filament\Admin\Resources\OrderResource\RelationManagers;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Shop';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make()
                    ->schema(static::getDetailsFormSchema()),

                Section::make()
                    ->schema([
                        static::getOrderItemsRepeater(),
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                ->label('Order #')
                ->sortable()
                ->searchable()
                ->badge()
                ->color('primary'),

                TextColumn::make('user.name')
                ->label('Customer')
                ->sortable()
                ->searchable(),

                TextColumn::make('order_status')
                ->label('Status')
                ->sortable()
                ->searchable()
                ->badge(),

                TextColumn::make('created_at')
                ->label('Order Date')
                ->sortable()
                ->searchable()
                ->dateTime('F d, Y  - g:i A'),

                TextColumn::make('payment_method')
                ->label('Payment Method')
                ->sortable()
                ->searchable()
                ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('order_status')
                    ->options(OrderStatusEnum::class),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options(PaymentMethodEnum::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->tooltip('Actions')
            ])
            ->bulkActions([

            ])
            ->deferLoading()
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->label(__('New Order')),
            ])
            ->emptyStateIcon('heroicon-o-shopping-cart')
            ->emptyStateHeading('No Orders are created')
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }


    /** @return Forms\Components\Component[] */
    public static function getDetailsFormSchema(): array
    {
        return [

            Fieldset::make('Order Details')
                ->schema([
                    TextInput::make('order_number')
                    ->label('Order #')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->maxLength(32)
                    ->default('#ORDER-'. date('His-') . strtoupper(Str::random(6)))
                    ->unique(Order::class, 'order_number', ignoreRecord: true),

                    Select::make('user_id')
                        ->label('Customer')
                        ->relationship('user', 'name')
                        ->preload()
                        ->optionsLimit(6)
                        ->native(false)
                        ->searchable()
                        // ->relationship(
                        //     name: 'user',
                        //     ignoreRecord: true,
                        //     modifyQueryUsing: fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'customer')),
                        // )
                        ->getOptionLabelFromRecordUsing(fn ($record) => ucwords($record->name)),

                    ToggleButtons::make('payment_method')
                        ->label('Shipping Method')
                        ->options(PaymentMethodEnum::class)
                        ->inline()
                        ->dehydrated()
                        ->default(PaymentMethodEnum::COD),

                    
                    ToggleButtons::make('payment_status')
                        ->label('Payment status')
                        ->options(PaymentStatusEnum::class)
                        ->inline()
                        ->dehydrated()
                        ->default(PaymentStatusEnum::PENDING),

                    TextInput::make('shipping_price')
                        ->label('Shipping Price')
                        ->numeric()
                        ->dehydrated()
                        ->required()
                        ->default(0)
                        ->placeholder('0.00'),

                    ToggleButtons::make('order_status')
                        ->inline()
                        ->options(OrderStatusEnum::class)
                        ->default(OrderStatusEnum::New)
                        ->dehydrated()
                        ->required()
                        ->columnSpanFull(),

                    Textarea::make('order_notes')
                        ->label('Notes')
                        ->maxLength(1500)
                        ->columnSpanFull()
                        ->rows(5)
                        ->placeholder('Any special instructions or notes for the order'),
                ]),


            Fieldset::make('Address')
                ->schema([
                    AddressForm::make('address')
                    ->relationship('address')
                    ->columnSpanFull(),

                ]),
        ];
    }


    public static function getOrderItemsRepeater(): Repeater
    {
        return Repeater::make('orderItems')
            ->relationship()
            ->schema([

                Select::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'prod_name')
                    ->preload()
                    ->optionsLimit(6)
                    ->native(false)
                    ->searchable()
                    ->reactive()
                    ->getOptionLabelFromRecordUsing(fn ($record) => ucwords($record->prod_name))
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('unit_price', Product::find($state)?->prod_price ?? 0))
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->required(),

                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->dehydrated()
                    ->required()
                    ->maxLength(12)
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $set('subtotal', $state * $get('unit_price'));
                    }),

                TextInput::make('unit_price')
                    ->label('Unit Price')
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $set('subtotal', $state * $get('unit_price'));
                    }),

                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->numeric()
                    ->dehydrated()
                    ->required()
                    ->default(0.00)
                    ->placeholder('0.00')
                    ->readOnly(),

            ])
            ->columns([
                'sm' => 1,
                'md' => 2,
                'lg' => 4
            ])
            ->defaultItems(1);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewOrder::class,
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Group::make([
                    RepeatableEntry::make('orderItems')
                    ->label('')
                    ->schema([

                        Split::make([
                            ImageEntry::make('product.prod_ft_image')
                            ->hiddenLabel()
                            ->size(60)
                            ->grow(false),

                            Group::make([
                                TextEntry::make('product.prod_name')
                                ->weight(FontWeight::ExtraBold),

                                TextEntry::make('quantity')
                                ->badge()
                                ->color('warning'),

                                TextEntry::make('subtotal')
                                ->weight(FontWeight::ExtraBold)
                                ->badge()
                                ->color('success')
                                ->formatStateUsing(fn ($state) => '₱ ' . number_format((float)$state, 2)),
                            ])
                            ->columns([
                                'sm' => 1,
                                'md' => 3,
                                'lg' => 3

                            ])
                        ])
                        ->from('md')

                    ])
                ])
                ->columnSpan([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 3
                ]),

                Group::make([
                    InfoSec::make()
                    ->schema([

                        TextEntry::make('order_status')
                        ->label('Status')
                        ->badge()
                        ->color(fn ($state, $record) => $record->order_status->getColor())
                        ->icon(fn ($state, $record) => $record->order_status->getIcon()),

                        TextEntry::make('created_at')
                        ->label('Order Date')
                        ->dateTime('F d, Y  - g:i A'),

                        TextEntry::make('addresses.full_address')
                        ->label('Address')
                        ->weight(FontWeight::ExtraBold)
                        ->columnSpanFull()

                    ])
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 2
                    ]),

                ])
                ->columnSpan([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 2
                ])

            ])
            ->columns([
                'sm' => 1,
                'md' => 4,
                'lg' => 5
            ]);
    }
}
