<?php

namespace App\Filament\Service\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Service;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Enums\VehicleTypeEnum;
use App\Models\ServiceRequest;
use App\Enums\RequestStatusEnum;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Split;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Section as InfoSec;
use App\Filament\Service\Resources\ServiceRequestResource\Pages;
use App\Filament\Service\Resources\ServiceRequestResource\RelationManagers;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'Services';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Wizard::make([
                    Step::make('Service Request Details')
                    ->icon('heroicon-o-shopping-bag')
                    ->completedIcon('heroicon-m-check-badge')
                    ->description('Enter the service request details.')
                    ->schema([
                        Split::make([
                            Group::make([

                                Section::make()
                                    ->schema([

                                        Group::make()
                                        ->schema([

                                            TextInput::make('service_number')
                                            ->label('Service Number')
                                            ->maxLength(255)
                                            ->required()
                                            ->default(fn () => static::generateServiceNumber())
                                            ->dehydrated()
                                            ->readOnly()
                                            ->suffixAction(
                                                Action::make('regenerateServiceNumber')
                                                ->icon('heroicon-o-arrow-path')
                                                ->tooltip('Generate new discount code')
                                                ->action(function (Get $get, Set $set){
                                                    $set('service_number', static::generateServiceNumber());
                                                })
                                            ),

                                            Select::make('user_id')
                                            ->relationship(
                                                name: 'customer',
                                                modifyQueryUsing: fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'customer'),
                                                ),
                                            )
                                            ->getOptionLabelFromRecordUsing(fn (Model $record) => ucwords($record->name) ?? '')
                                            ->searchable('name')
                                            ->preload()
                                            ->required()
                                            ->native(false)
                                            ->searchable()
                                            ->optionsLimit(6)
                                            ->createOptionForm([
                                                Split::make([
                                                    Section::make('User Details')
                                                    ->description('The user\'s name and email address.')
                                                    ->schema([

                                                        Group::make()
                                                        ->schema([
                                                            TextInput::make('name')
                                                            ->required()
                                                            ->maxLength(255),

                                                            TextInput::make('email')
                                                            ->required()
                                                            ->email()
                                                            ->unique(ignoreRecord: true),
                                                        ])
                                                        ->columns([
                                                            'sm' => 1,
                                                            'md' => 2,
                                                            'lg' => 2
                                                        ]),

                                                        TextInput::make('password')
                                                        ->password()
                                                        ->revealable()
                                                        ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                                                        ->required(fn (Page $livewire): bool => $livewire instanceof Pages\EditUser),
                                                        // ->visible(fn (Page $livewire): bool => $livewire instanceof Pages\CreateUser),

                                                        TextInput::make('password_confirmation')
                                                        ->label('Confirm Password')
                                                        ->password()
                                                        ->revealable()
                                                        ->required(fn (Page $livewire): bool => $livewire instanceof Pages\EditUser),
                                                        // ->visible(fn (Page $livewire): bool => $livewire instanceof Pages\CreateUser),
                                                    ])
                                                    ->columns(1),

                                                ])
                                                ->columns([
                                                    'sm' => 1,
                                                    'md' => 2,
                                                ])
                                                ->columnSpanFull(),

                                                Section::make('Roles')
                                                ->description('Select roles for this user')
                                                ->schema([

                                                    CheckboxList::make('roles')
                                                    ->label('Select Roles')
                                                    ->relationship(name: 'roles', titleAttribute: 'name')
                                                    ->searchable()
                                                    ->columns(2)
                                                    ->options(function () {
                                                        return Role::all()->mapWithKeys(function ($role) {
                                                            return [$role->id => Str::replace('_', ' ', Str::ucwords($role->name))];
                                                        });
                                                    })

                                                ])->columnSpanFull()

                                            ]),


                                        ])
                                        ->columns([
                                            'sm' => 1,
                                            'md' => 2,
                                            'lg' => 2
                                        ]),

                                        Group::make()
                                        ->schema([
                                            Select::make('mechanic_id')
                                            ->label('Mechanic')
                                            ->required()
                                            ->relationship(
                                                name: 'mechanic',
                                                modifyQueryUsing: fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'mechanic')),
                                            )
                                            ->getOptionLabelFromRecordUsing(fn (Model $record) => ucwords($record->name) ?? '')
                                            ->preload()
                                            ->optionsLimit(6)
                                            ->native(false)
                                            ->searchable()
                                            ->createOptionForm([
                                                Split::make([
                                                    Section::make('Mechanic user details')
                                                        ->description('The user\'s name and email address.')
                                                        ->schema([
                                                            Group::make()
                                                                ->schema([
                                                                    TextInput::make('name')
                                                                        ->required()
                                                                        ->maxLength(255),

                                                                    TextInput::make('email')
                                                                        ->required()
                                                                        ->email()
                                                                        ->unique(ignoreRecord: true),
                                                                ])
                                                                ->columns([
                                                                    'sm' => 1,
                                                                    'md' => 2,
                                                                    'lg' => 2
                                                                ]),

                                                            TextInput::make('password')
                                                                ->password()
                                                                ->revealable()
                                                                ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                                                                ->required(),

                                                            TextInput::make('password_confirmation')
                                                                ->label('Confirm Password')
                                                                ->password()
                                                                ->revealable()
                                                                ->required()
                                                                ->same('password'),
                                                        ])
                                                        ->columns(1),
                                                ])
                                                ->columns([
                                                    'sm' => 1,
                                                    'md' => 2,
                                                ])
                                                ->columnSpanFull(),
                                            ])
                                            ->createOptionUsing(function (array $data): Model {
                                                // Create the user
                                                $user = \App\Models\User::create([
                                                    'name' => $data['name'],
                                                    'email' => $data['email'],
                                                    'password' => bcrypt($data['password']),
                                                ]);

                                                // Automatically assign the mechanic role
                                                $user->assignRole('mechanic');

                                                return $user;
                                            }),

                                            ToggleButtons::make('vehicle_type')
                                                ->label('Vehicle Type')
                                                ->required()
                                                ->options(VehicleTypeEnum::class)
                                                ->inline()
                                                ->dehydrated()
                                                ->default(VehicleTypeEnum::Motorcycle),
                                        ])
                                         ->columns([
                                            'sm' => 1,
                                            'md' => 2,
                                            'lg' => 2
                                        ]),


                                    ]),

                                Section::make()
                                    ->schema([
                                        RichEditor::make('remarks')
                                        ->required()
                                        ->maxLength(65535)
                                    ])

                            ]),
                            Section::make([
                                DatePicker::make('requested_date')
                                    ->native(false)
                                    ->required()
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->minDate(today())  // Prevents selecting past dates
                                    ->default(today())  // Sets current date as default
                                    ->disabled()
                                    ->dehydrated(),
                                DatePicker::make('scheduled_date')
                                    ->native(false)
                                    ->required()
                                    ->prefixIcon('heroicon-o-calendar-date-range')
                                    ->minDate(today())
                                    ->maxDate(today()->addYears(3))
                                    ->dehydrated(),

                                ToggleButtons::make('status')
                                    ->label('Status')
                                    ->options(RequestStatusEnum::class)
                                    ->dehydrated()
                                    ->default(RequestStatusEnum::PENDING)
                            ])->grow(false),

                        ])
                        ->columnSpanFull()
                        ->from('md')
                    ]),

                    Step::make('Request Items')
                    ->icon('heroicon-o-shopping-cart')
                    ->completedIcon('heroicon-m-check-badge')
                    ->description('Add Items to the Request')
                    ->schema([
                        Section::make()
                        ->schema([
                            Repeater::make('items')
                            ->label('')
                            ->relationship()
                            ->schema([

                                Select::make('service_id')
                                    ->relationship('service', 'service_name')
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->getOptionLabelFromRecordUsing(fn ($record) => ucwords($record->service_name) . "   (₱{$record->service_price})")
                                    ->preload()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->optionsLimit(6)
                                    ->reactive()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $service = Service::query()->find($state);
                                        if ($service) {
                                            $set('serv_price', $service->service_price ?? 0);
                                            $set('labor_price', $service->service_standard_labor ?? 0);
                                            // Calculate subtotal immediately
                                            $set('subtotal_price', ($service->service_standard_labor ?? 0) + ($service->service_price ?? 0));
                                        } else {
                                            $set('serv_price', 0);
                                            $set('labor_price', 0);
                                            $set('subtotal_price', 0);
                                        }
                                    })
                                    ->distinct()
                                    ->columnSpan(2),

                                TextInput::make('serv_price')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->default(0)
                                    ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get) {
                                        $serviceId = $get('service_id');
                                        if ($serviceId) {
                                            $service = Service::query()->find($serviceId);
                                            if ($service) {
                                                $set('serv_price', $service->service_price);
                                            }
                                        }
                                    })
                                    ->prefix('₱')
                                    ->suffix('.00')
                                    ->reactive()
                                    ->columnSpan(1),

                                TextInput::make('labor_price')
                                    ->label('Labor')
                                    ->numeric()
                                    ->dehydrated()
                                    ->required()
                                    ->minValue(1)
                                    ->maxLength(12)
                                    ->live(onBlur: true)
                                    ->reactive()
                                    ->default(0)
                                    ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get) {
                                        $serviceId = $get('service_id');
                                        if ($serviceId) {
                                            $service = Service::query()->find($serviceId);
                                            if ($service) {
                                                $set('labor_price', $service->service_standard_labor);
                                                // Also set subtotal when hydrating
                                                $set('subtotal_price', $service->service_standard_labor + $service->service_price);
                                            }
                                        }
                                    })
                                    ->prefix('₱')
                                    ->suffix('.00')
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $set('subtotal_price', $state + $get('serv_price'));
                                    })
                                    ->columnSpan(1),

                                TextInput::make('subtotal_price')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->default(0.00)
                                    ->placeholder('0.00')
                                    ->prefix('₱')
                                    ->suffix('.00')
                                    ->readOnly()
                                    ->columnSpan(1),
                            ])
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                                'lg' => 5
                            ])
                            ->addActionLabel('Add another service')
                            ->itemLabel(fn (array $state): ?string => Service::query()->find($state['service_id'])->service_name ?? null)
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                return $data;
                            })
                        ])
                    ])

                ])
                ->skippable(false)
                ->contained(false)
                ->columnspanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('service_number')
                ->sortable()
                ->searchable()
                ->badge()
                ->color('primary'),

                TextColumn::make('customer.name')
                ->sortable()
                ->searchable(),

                TextColumn::make('mechanic.name')
                ->sortable()
                ->searchable()
                ->badge()
                ->color('primary'),

                TextColumn::make('vehicle_type')
                ->sortable()
                ->searchable()
                ->badge()
                ->colors([
                    'primary' => 'car',
                    'success' => 'motorcycle',
                    'warning' => 'other',
                ])
                ->formatStateUsing(function ($state) {
                    return ucwords($state);
                }),

                TextColumn::make('status')
                ->sortable()
                ->searchable()
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'warning',
                    'in_progress' => 'info',
                    'completed' => 'success',
                    'cancelled' => 'danger',
                    default => 'gray',
                })
                ->icon(fn (string $state): ?string => match ($state) {
                    'pending' => 'heroicon-m-clock',
                    'in_progress' => 'heroicon-m-arrow-path',
                    'completed' => 'heroicon-m-check-badge',
                    'cancelled' => 'heroicon-m-x-circle',
                    default => null,
                })
                ->formatStateUsing(function ($state) {
                    return ucwords(str_replace('_', ' ', $state));
                }),

                TextColumn::make('created_at')
                ->sortable()
                ->since(),
                // ->toggleable(isToggledHiddenByDefault: true),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->tooltip('Actions')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                return match (true) {
                    $user->hasRole('mechanic') => $query->where('mechanic_id', $user->id),
                };
            })
            ->deferLoading()
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->label(__('Create Service Request')),
            ])
            ->emptyStateIcon('heroicon-o-bell-alert')
            ->emptyStateHeading('No Service Requests are created');
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
            'index' => Pages\ListServiceRequests::route('/'),
            'create' => Pages\CreateServiceRequest::route('/create'),
            'edit' => Pages\EditServiceRequest::route('/{record}/edit'),
        ];
    }

    protected static function generateServiceNumber(): string
    {
        $alpha = Str::upper(Str::random(4));
        $numeric = str_pad(random_int(0, pow(10, 2) - 1), 2, '0', STR_PAD_LEFT);

         return 'SRV-NUM-' . str_shuffle($alpha . $numeric);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                InfoSec::make()
                ->schema([
                    RepeatableEntry::make('items')
                    ->label('Request Services')
                    ->schema([

                        TextEntry::make('service.service_name')
                        ->label('Service Name')
                        ->weight(FontWeight::ExtraBold)
                        ->icon('heroicon-m-wrench-screwdriver')
                        ->color('primary'),

                        TextEntry::make('service.service_price')
                        ->label('Service Price')
                        ->weight(FontWeight::ExtraBold)
                        ->money('PHP')
                        ->badge()
                        ->color('success'),
                    ])
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                ])
                ->columnSpan([
                    'sm' => 1,
                    'md' => 4,
                    'lg' => 4
                ]),

                InfoSec::make()
                ->schema([

                    TextEntry::make('service_number')
                    ->label('Service Number')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::ExtraBold)
                    ->color('primary')
                    ->icon('heroicon-o-hashtag'),

                    TextEntry::make('customer.name')
                    ->label('Customer Name')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->icon('heroicon-o-user')
                    ->weight(FontWeight::ExtraBold),

                    TextEntry::make('status')
                    ->label('Status')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->icon('heroicon-m-check-badge')
                    ->weight(FontWeight::ExtraBold)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): ?string => match ($state) {
                        'pending' => 'heroicon-m-clock',
                        'in_progress' => 'heroicon-m-arrow-path',
                        'completed' => 'heroicon-m-check-badge',
                        'cancelled' => 'heroicon-m-x-circle',
                        default => null,
                    })
                    ->formatStateUsing(function ($state) {
                        return ucwords(str_replace('_', ' ', $state));
                    }),

                    TextEntry::make('mechanic.name')
                    ->label('Mechanic Name')
                    ->icon('heroicon-o-wrench')
                    ->weight(FontWeight::ExtraBold)
                    ->badge()
                    ->color('success'),
                ])
                ->columnSpan([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 2
                ])

            ])
            ->columns([
                'sm' => 1,
                'md' => 6,
                'lg' => 6
            ])
            ;
    }
}
