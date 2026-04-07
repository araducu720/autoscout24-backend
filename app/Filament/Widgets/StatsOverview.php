<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use App\Models\ContactMessage;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Vehicles', Vehicle::count())
                ->description('All vehicles in database')
                ->descriptionIcon('heroicon-m-truck')
                ->color('success'),
            Stat::make('Active Vehicles', Vehicle::where('status', 'active')->count())
                ->description('Currently available')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),
            Stat::make('Featured Vehicles', Vehicle::where('is_featured', true)->count())
                ->description('Featured listings')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
            Stat::make('New Messages', ContactMessage::where('status', 'new')->count())
                ->description('Unread messages')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('danger'),
        ];
    }
}
