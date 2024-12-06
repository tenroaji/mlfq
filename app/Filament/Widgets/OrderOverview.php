<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class OrderOverview extends BaseWidget
{use HasWidgetShield;
    protected function getStats(): array
    {

        // Query untuk menghitung statistik
        // $query = DetailTransaksi::query();
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $currentDay = now()->day;

        $currentMonthStart = now()->startOfMonth();  // First date of the current month
        $currentMonthEnd = now()->endOfMonth();    // Last date of the current month

        $currentDayString = now()->toDateString();

        $pemasukanPrioritas = Order::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();
        $orderanHarian = Order::whereDay('created_at', $currentDay)
            ->count();
        $pemasukanHarian = Order::whereDay('created_at', $currentDay)
            ->sum('sum_price');
        $pemasukanBulanan = Order::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('sum_price');

        $user = Auth::user();
        if ($user->hasRole('designer')) {
            return [
                Stat::make('Orderan Hari ini', number_format($orderanHarian))
                    ->url(OrderResource::getUrl('index',['tableFilters[created_at][created_from]' => $currentDayString,'tableFilters[created_at][created_until]' => $currentDayString])),
            ];
        }else{
            return [
                Stat::make('Orderan Bulan ini', number_format($pemasukanPrioritas))
                    ->url(OrderResource::getUrl('index',['tableFilters[created_at][created_from]' => $currentMonthStart,'tableFilters[created_at][created_until]' => $currentMonthEnd])),
                Stat::make('Pemasukan Bulan ini', number_format($pemasukanBulanan))
                    ->url(OrderResource::getUrl('index',['tableFilters[created_at][created_from]' => $currentMonthStart,'tableFilters[created_at][created_until]' => $currentMonthEnd])),
                Stat::make('Orderan Hari ini', number_format($orderanHarian))
                    ->url(OrderResource::getUrl('index',['tableFilters[created_at][created_from]' => $currentDayString,'tableFilters[created_at][created_until]' => $currentDayString])),
                Stat::make('Pemasukan Hari ini', number_format($pemasukanHarian))
                    ->url(OrderResource::getUrl('index',['tableFilters[created_at][created_from]' => $currentDayString,'tableFilters[created_at][created_until]' => $currentDayString])),

            ];
        }


    }
}
