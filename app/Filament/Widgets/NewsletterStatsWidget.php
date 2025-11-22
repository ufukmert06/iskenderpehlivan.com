<?php

namespace App\Filament\Widgets;

use App\Models\Newsletter;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewsletterStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $totalSubscribers = Newsletter::count();
        $activeSubscribers = Newsletter::where('status', 'active')->count();
        $unsubscribed = Newsletter::where('status', 'unsubscribed')->count();

        // Bu ayki aboneler
        $thisMonthSubscribers = Newsletter::where('status', 'active')
            ->whereMonth('subscribed_at', now()->month)
            ->whereYear('subscribed_at', now()->year)
            ->count();

        // Bugünkü aboneler
        $todaySubscribers = Newsletter::where('status', 'active')
            ->whereDate('subscribed_at', now()->toDateString())
            ->count();

        // Bu haftaki aboneler
        $thisWeekSubscribers = Newsletter::where('status', 'active')
            ->whereBetween('subscribed_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        return [
            Stat::make('Toplam Abone', $totalSubscribers)
                ->description('Tüm zamanlar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Aktif Aboneler', $activeSubscribers)
                ->description('Şu anda aktif')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Abonelikten Çıkanlar', $unsubscribed)
                ->description('Toplam çıkan')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Bu Ay', $thisMonthSubscribers)
                ->description(now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Bu Hafta', $thisWeekSubscribers)
                ->description('Son 7 gün')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),

            Stat::make('Bugün', $todaySubscribers)
                ->description(now()->format('d.m.Y'))
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('success'),
        ];
    }
}
