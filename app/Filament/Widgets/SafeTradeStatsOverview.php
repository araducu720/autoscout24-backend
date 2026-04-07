<?php

namespace App\Filament\Widgets;

use App\Models\SafetradeTransaction;
use App\Models\EscrowTransaction;
use App\Models\Dealer;
use App\Filament\Resources\SafetradeTransactionResource;
use App\Filament\Resources\EscrowTransactionResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class SafeTradeStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getHeading(): ?string
    {
        return 'SafeTrade Overview';
    }

    protected function getStats(): array
    {
        $transactionsThisMonth = SafetradeTransaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
        
        $completedThisMonth = (clone $transactionsThisMonth)->where('status', 'completed')->count();
        $volumeThisMonth = (clone $transactionsThisMonth)->where('status', 'completed')->sum('vehicle_price');
        
        $lastMonth = SafetradeTransaction::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->where('status', 'completed')
            ->count();
        
        $change = $lastMonth > 0 
            ? round((($completedThisMonth - $lastMonth) / $lastMonth) * 100, 1)
            : 0;

        $activeCount = SafetradeTransaction::whereNotIn('status', ['completed', 'cancelled', 'refunded'])->count();
        $disputedCount = EscrowTransaction::where('status', 'disputed')->count();
        $pendingFunding = SafetradeTransaction::whereIn('escrow_status', ['pending', 'awaiting_verification'])->count();

        return [
            Stat::make('Active Transactions', $activeCount)
                ->description('Currently in progress')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('primary')
                ->url(SafetradeTransactionResource::getUrl('index'))
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]),
                
            Stat::make('Pending Funding', $pendingFunding)
                ->description('Awaiting escrow payment')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingFunding > 0 ? 'warning' : 'success')
                ->url(SafetradeTransactionResource::getUrl('index')),
                
            Stat::make('Disputes', $disputedCount)
                ->description('Escrow disputes open')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($disputedCount > 0 ? 'danger' : 'success')
                ->url(EscrowTransactionResource::getUrl('index')),
                
            Stat::make('Completed This Month', $completedThisMonth)
                ->description($change >= 0 ? "+{$change}% from last month" : "{$change}% from last month")
                ->descriptionIcon($change >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($change >= 0 ? 'success' : 'danger'),
                
            Stat::make('Volume This Month', '€' . Number::abbreviate($volumeThisMonth, 1))
                ->description('Total transaction value')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
                
            Stat::make('Active Dealers', Dealer::where('is_verified', true)->count())
                ->description(Dealer::where('is_verified', false)->count() . ' pending verification')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('primary'),
        ];
    }
}
