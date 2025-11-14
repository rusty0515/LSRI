<div>
    <div class="max-w-[60rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-12 mx-auto">

        <!-- Header -->
        <div class="text-center mx-auto mb-5">
            <h2 class="text-2xl text-gray-800 dark:text-white flex flex-row gap-x-2 items-center align-middle justify-center">
                <svg class="size-7 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
                {{ __('Dashboard') }}
            </h2>
        </div>

        <!-- Segment Navigation -->
        <div class="flex justify-center">
            <div class="flex bg-gray-100 hover:bg-gray-200 rounded-lg transition p-1 dark:bg-neutral-700 dark:hover:bg-neutral-600">
                <nav class="flex justify-center gap-x-2" aria-label="Tabs" role="tablist">
                    <button type="button" wire:click="setActiveSegment('orders')"
                        class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $activeSegment === 'orders' ? 'bg-white text-gray-700 dark:bg-neutral-800 dark:text-neutral-400 shadow-xs' : 'bg-transparent text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-white' }}">
                        <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                        {{ __('Orders') }}
                    </button>
                    
                    <button type="button" wire:click="setActiveSegment('requests')"
                        class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $activeSegment === 'requests' ? 'bg-white text-gray-700 dark:bg-neutral-800 dark:text-neutral-400 shadow-xs' : 'bg-transparent text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-white' }}">
                        <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                        {{ __('Request') }}
                    </button>

                    <button type="button" wire:click="setActiveSegment('vehicles')"
                        class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $activeSegment === 'vehicles' ? 'bg-white text-gray-700 dark:bg-neutral-800 dark:text-neutral-400 shadow-xs' : 'bg-transparent text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-white' }}">
                        <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                        {{ __('Vehicles') }}
                    </button>

                    <a href="{{ route('page.checkout') }}"
                        class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-transparent text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-white">
                        <svg class="size-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                        {{ __('Checkout') }}
                    </a>
                </nav>
            </div>
        </div>

        <!-- Segment Content -->
        <div class="mt-3">
            <!-- Orders Segment -->
            @if($activeSegment === 'orders')
                <div class="flex flex-row align-items-center items-center justify-between gap-x-3 text-gray-800 dark:text-neutral-200 mb-5">
                    <h4 class="font-semibold">{{ __('Your orders') }}</h4>
                    <a href="{{ route('page.shop') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-500 border border-transparent rounded-lg gap-x-2 hover:bg-red-600 focus:outline-none focus:bg-red-600 disabled:opacity-50 disabled:pointer-events-none">
                        <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        {{ __('Get Order') }}
                    </a>
                </div>

                <!-- Order Tabs -->
                <div class="flex">
                    <div class="border-e-2 border-gray-200 dark:border-neutral-700">
                        <nav class="flex flex-col space-y-2 md:space-y-4 lg:space-y-5" aria-label="Tabs" role="tablist" aria-orientation="vertical">
                            @foreach(['in_progress' => ['icon' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99', 'label' => 'In Progress'], 
                                     'shipped' => ['icon' => 'M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12', 'label' => 'Shipped'],
                                     'delivered' => ['icon' => 'm21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9', 'label' => 'Delivered'],
                                     'cancelled' => ['icon' => 'm9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'label' => 'Cancelled']] as $tab => $config)
                                <button type="button" wire:click="setActiveOrderTab('{{ $tab }}')"
                                    class="py-1 pe-4 inline-flex items-center gap-x-2 border-e-2 text-sm whitespace-nowrap transition-all duration-200 {{ $activeOrderTab === $tab ? 'border-red-500 text-red-600 dark:text-red-600' : 'border-transparent text-gray-500 hover:text-red-600 dark:text-neutral-400 dark:hover:text-red-500' }}">
                                    <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $config['icon'] }}" />
                                    </svg>
                                    {{ __($config['label']) }}
                                </button>
                            @endforeach
                        </nav>
                    </div>

                    <div class="ms-3 w-full">
                        <!-- Order Content -->
                        @if($orders->count() > 0)
                            <div class="border border-gray-200 rounded-lg overflow-hidden dark:border-neutral-700">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Order #</th>
                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Date</th>
                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Total</th>
                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Status</th>
                                            <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                        @foreach($orders as $order)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $order->order_number }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $order->created_at->format('M d, Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">₱{{ number_format($order->order_total_price, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-500">
                                                        {{ $order->order_status->getLabel() }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                    <button type="button" class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400">View</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto size-12 text-gray-400 dark:text-neutral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No orders</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Get started by creating a new order.</p>
                            </div>
                        @endif
                    </div>
                </div>

            <!-- Requests Segment -->
            @elseif($activeSegment === 'requests')
                <div class="flex flex-row align-items-center items-center justify-between gap-x-3 text-gray-800 dark:text-neutral-200 mb-5">
                    <h4 class="font-semibold">{{ __('Your Requests') }}</h4>
                    <button type="button" wire:click="openRequestModal"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-500 border border-transparent rounded-lg gap-x-2 hover:bg-red-600 focus:outline-none focus:bg-red-600 disabled:opacity-50 disabled:pointer-events-none">
                        <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        {{ __('New Request') }}
                    </button>
                </div>

                <!-- Request Tabs -->
                <div class="flex">
                    <div class="border-e-2 border-gray-200 dark:border-neutral-700">
                        <nav class="flex flex-col space-y-2 md:space-y-4 lg:space-y-5" aria-label="Tabs" role="tablist" aria-orientation="vertical">
                            @foreach(['pending' => ['icon' => 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z', 'label' => 'Pending'], 
                                     'in_progress' => ['icon' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99', 'label' => 'In Progress'],
                                     'completed' => ['icon' => 'm4.5 12.75 6 6 9-13.5', 'label' => 'Completed'],
                                     'cancelled' => ['icon' => 'm9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'label' => 'Cancelled']] as $tab => $config)
                                <button type="button" wire:click="setActiveRequestTab('{{ $tab }}')"
                                    class="py-1 pe-4 inline-flex items-center gap-x-2 border-e-2 text-sm whitespace-nowrap transition-all duration-200 {{ $activeRequestTab === $tab ? 'border-red-500 text-red-600 dark:text-red-600' : 'border-transparent text-gray-500 hover:text-red-600 dark:text-neutral-400 dark:hover:text-red-500' }}">
                                    <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $config['icon'] }}" />
                                    </svg>
                                    {{ __($config['label']) }}
                                </button>
                            @endforeach
                        </nav>
                    </div>

                    <div class="ms-3 w-full">
                        <!-- Request Content -->
                        @if($serviceRequests->count() > 0)
                            <div class="border border-gray-200 rounded-lg overflow-hidden dark:border-neutral-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead>
                    <tr>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Service #</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Vehicle</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Mechanic</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Scheduled</th>
        @if(!in_array($activeRequestTab, ['cancelled', 'completed', 'in_progress']))
            <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Action</th>
        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @foreach($serviceRequests as $request)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $request->service_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                {{ ucfirst($request->vehicle_type) }}
                                @if($request->vehicle_type === 'other' && $request->remarks)
                                    <span class="text-xs text-gray-500">(Custom)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                {{ $request->mechanic->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                {{ $request->scheduled_date ? \Carbon\Carbon::parse($request->scheduled_date)->format('M d, Y') : 'Not scheduled' }}
                            </td>
                           
                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                            @if(in_array($request->status, ['pending']))
                                                <button type="button" wire:click="cancelRequest({{ $request->id }})" 
                                                    class="inline-flex items-center text-sm font-semibold rounded-lg border border-transparent text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400">
                                                    Cancel
                                                </button>
                                            @endif
                                        </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
                            <div class="text-center py-8">
                                <svg class="mx-auto size-12 text-gray-400 dark:text-neutral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No service requests</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Get started by creating a new service request.</p>
                            </div>
                        @endif
                    </div>
                </div>

            <!-- Vehicles Segment -->
            @elseif($activeSegment === 'vehicles')
                <!-- <div class="flex flex-row align-items-center items-center justify-between gap-x-3 text-gray-800 dark:text-neutral-200 mb-5">
                    <h4 class="font-semibold">{{ __('Your Vehicles') }}</h4>
                    <button type="button" wire:click="openRequestModal"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-500 border border-transparent rounded-lg gap-x-2 hover:bg-red-600 focus:outline-none focus:bg-red-600 disabled:opacity-50 disabled:pointer-events-none">
                        <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        {{ __('Add Service Request') }}
                    </button>
                </div> -->

                <!-- Vehicle Content -->
                @if($vehicles->count() > 0)
                    <div class="border border-gray-200 rounded-lg overflow-hidden dark:border-neutral-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Vehicle Type</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Brand & Model</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Services</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">First Service</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Last Service</th>
                                    <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                @foreach($vehicles as $vehicle)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                            <div class="flex items-center gap-2">
                                                @if($vehicle['type'] === 'car')
                                                    <svg class="size-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                    </svg>
                                                @elseif($vehicle['type'] === 'motorcycle')
                                                    <svg class="size-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                    </svg>
                                                @else
                                                    <svg class="size-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                @endif
                                                {{ $vehicle['type_label'] }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                            {{ $vehicle['brand_model'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                            <span class="inline-flex items-center gap-x-1.5 py-1 px-2 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-500">
                                                {{ $vehicle['service_count'] }} service(s)
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                            {{ $vehicle['first_used']->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                            {{ $vehicle['last_used']->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                            <button type="button" wire:click="deleteVehicle('{{ $vehicle['type'] }}', '{{ $vehicle['remarks'] }}')"
                                                class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400"
                                                onclick="return confirm('Are you sure you want to delete all service history for this vehicle?')">
                                                Delete History
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto size-12 text-gray-400 dark:text-neutral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No vehicles found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Get started by creating a service request for your vehicle.</p>
                        <button type="button" wire:click="openRequestModal" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700">
                            Create Service Request
                        </button>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- New Request Modal -->
    @if($showRequestModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity backdrop-blur bg-opacity-10" wire:click="closeRequestModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 dark:bg-neutral-800">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between pb-3">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">New Service Request</h3>
                        <button type="button" wire:click="closeRequestModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal body -->
                    <div class="space-y-4">
                        <!-- Vehicle Type Selection -->
                        <!-- Vehicle Type Selection -->
<div>
    <label class="block mb-2 text-sm font-medium dark:text-white">Select Vehicle Type</label>
    <div class="grid grid-cols-3 gap-3">
        @foreach(['car' => 'Car', 'motorcycle' => 'Motorcycle', 'other' => 'Other'] as $value => $label)
            <label class="flex flex-col items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-neutral-700 dark:hover:bg-neutral-700 cursor-pointer {{ $selectedVehicleType === $value ? 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800' : '' }}">
                <input type="radio" wire:model.live="selectedVehicleType" value="{{ $value }}" 
                    class="shrink-0 border-gray-300 rounded-full text-red-600 focus:ring-red-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:checked:bg-red-500 dark:checked:border-red-500 dark:focus:ring-offset-gray-800">
                <span class="mt-2 text-sm font-medium text-gray-800 dark:text-white">{{ $label }}</span>
            </label>
        @endforeach
    </div>
    @error('selectedVehicleType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
</div>

<!-- Vehicle Details for "Other" type -->
@if($selectedVehicleType === 'other')
    <div class="grid grid-cols-2 gap-4 transition-all duration-300 ease-in-out" 
         x-data x-transition>
        <div>
            <label for="vehicleBrand" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">Brand</label>
            <input type="text" id="vehicleBrand" wire:model="vehicleBrand" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white"
                placeholder="Enter vehicle brand">
            @error('vehicleBrand') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="vehicleModel" class="block mb-2 text-sm text-gray-700 font-medium dark:text-white">Model</label>
            <input type="text" id="vehicleModel" wire:model="vehicleModel" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white"
                placeholder="Enter vehicle model">
            @error('vehicleModel') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>
@endif

                        <!-- Mechanic Selection -->
                        <div>
                            <label for="selectedMechanic" class="block mb-2 text-sm font-medium dark:text-white">Select Mechanic</label>
                            <select id="selectedMechanic" wire:model="selectedMechanic" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                <option value="">Select Mechanic</option>
                                @foreach($mechanics as $mechanic)
                                    <option value="{{ $mechanic->id }}">{{ $mechanic->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedMechanic') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Date Selection -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="requestedDate" class="block mb-2 text-sm font-medium dark:text-white">Requested Date</label>
                                <input type="date" id="requestedDate" wire:model="requestedDate" required min="{{ now()->format('Y-m-d') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                @error('requestedDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="scheduledDate" class="block mb-2 text-sm font-medium dark:text-white">Scheduled Date</label>
                                <input type="date" id="scheduledDate" wire:model="scheduledDate" required min="{{ now()->format('Y-m-d') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                @error('scheduledDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Services Selection -->
                        <div>
                            <label class="block mb-2 text-sm font-medium dark:text-white">Select Services</label>
                            @error('selectedServices') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            
                            <div class="space-y-2 max-h-60 overflow-y-auto">
                                @foreach($services as $service)
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-neutral-700 dark:hover:bg-neutral-700 cursor-pointer">
                                        <input type="checkbox" wire:model="selectedServices" value="{{ $service->id }}" required
                                            class="shrink-0 border-gray-300 rounded text-red-600 focus:ring-red-500 dark:bg-neutral-800 dark:border-neutral-600 dark:checked:bg-red-500 dark:checked:border-red-500">
                                        <div class="ms-3">
                                            <span class="block text-sm font-semibold text-gray-800 dark:text-white">
                                                {{ $service->service_name }}
                                            </span>
                                            <span class="block text-sm text-gray-600 dark:text-neutral-400">
                                                ₱{{ number_format($service->service_price, 2) }}
                                            </span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                           
                        </div>

                        <!-- Additional Notes -->
                        <div>
                            <label for="message" class="block mb-2 text-sm font-medium dark:text-white">Additional Notes</label>
                            <textarea id="message" wire:model="message" rows="3" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white"
                                placeholder="Any additional information about your service request..."></textarea>
                            @error('message') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" wire:click="closeRequestModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-neutral-700 dark:text-white dark:border-neutral-600 dark:hover:bg-neutral-600">
                            Cancel
                        </button>
                        <button type="button" wire:click="saveRequest" wire:loading.attr="disabled"
    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50">
    <span >Submit Request</span>
    <!-- <span wire:loading>Submitting...</span> -->
</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>