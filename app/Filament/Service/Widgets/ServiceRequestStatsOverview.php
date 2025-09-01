<?php

namespace App\Filament\Service\Widgets;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ServiceRequestStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Get currently logged-in user
        $user = Auth::user();
        $today = Carbon::today();

        // Base query for current user's service requests
        $userRequestsQuery = ServiceRequest::query();

        // Filter based on user role
        if ($user->hasRole('mechanic')) {
            $userRequestsQuery->where('mechanic_id', $user->id);
        }

        // Requests per day (today) for current user
        $requestsToday = (clone $userRequestsQuery)
            ->whereDate('requested_date', $today)
            ->count();

        // Status counts for current user
        $pendingCount = (clone $userRequestsQuery)->where('status', 'pending')->count();
        $inProgressCount = (clone $userRequestsQuery)->where('status', 'in_progress')->count();
        $completedCount = (clone $userRequestsQuery)->where('status', 'completed')->count();
        $cancelledCount = (clone $userRequestsQuery)->where('status', 'cancelled')->count();

        // Total requests for current user
        $totalRequests = (clone $userRequestsQuery)->count();

        // Calculate percentages
        $pendingPercentage = $totalRequests > 0 ? round(($pendingCount / $totalRequests) * 100, 1) : 0;
        $completedPercentage = $totalRequests > 0 ? round(($completedCount / $totalRequests) * 100, 1) : 0;

        // Total income for today (for current user's completed requests)
        $totalIncomeToday = ServiceRequestItem::whereHas('serviceRequest', function ($query) use ($today, $user) {
            $query->where('status', 'completed')
                  ->whereDate('requested_date', $today);

            // Apply user-specific filtering
            if ($user->hasRole('mechanic')) {
                $query->where('mechanic_id', $user->id);
            }
        })->sum('subtotal_price');

        // Dynamic labels based on user role
        $rolePrefix = '';
        if ($user->hasRole('mechanic')) {
            $rolePrefix = 'My ';
        }

        return [
            Stat::make($rolePrefix . 'Requests Today', $requestsToday)
                ->description($user->hasRole('customer') ? 'Your requests made today' :
                            ($user->hasRole('mechanic') ? 'Requests assigned to you today' : 'Service requests made today'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make($rolePrefix . 'Pending', $pendingCount)
                ->description("{$pendingPercentage}% of your total requests")
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([3, 3, 4, 5, 6, 3, 5, 4]),

            Stat::make($rolePrefix . 'Completed', $completedCount)
                ->description("{$completedPercentage}% of your total requests")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([2, 4, 6, 8, 10, 8, 6, 9]),

            Stat::make($rolePrefix . 'Income Today', '₱' . number_format($totalIncomeToday, 2))
                ->description($user->hasRole('customer') ? 'Total spent on completed services today' :
                            ($user->hasRole('mechanic') ? 'Revenue from your completed services today' : 'Revenue from completed services today'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([100, 200, 150, 300, 250, 400, 350]),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
