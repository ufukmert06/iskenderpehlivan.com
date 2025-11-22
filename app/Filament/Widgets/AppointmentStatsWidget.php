<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AppointmentStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $totalAppointments = Appointment::count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $confirmedAppointments = Appointment::where('status', 'confirmed')->count();
        $completedAppointments = Appointment::where('status', 'completed')->count();

        // Bu ayki randevular
        $thisMonthAppointments = Appointment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Bugünkü randevular
        $todayAppointments = Appointment::whereDate('preferred_date', now()->toDateString())->count();

        return [
            Stat::make('Toplam Randevu', $totalAppointments)
                ->description('Tüm zamanlar')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('Bekleyen Randevular', $pendingAppointments)
                ->description('Onay bekliyor')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Onaylanan Randevular', $confirmedAppointments)
                ->description('Onaylanmış randevular')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),

            Stat::make('Tamamlanan Randevular', $completedAppointments)
                ->description('Tamamlanmış randevular')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Bu Ay', $thisMonthAppointments)
                ->description(now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Bugünkü Randevular', $todayAppointments)
                ->description(now()->format('d.m.Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}
