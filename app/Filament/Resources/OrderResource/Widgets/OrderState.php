<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Faker\Core\Number;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderState extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Order', Order::query()->where('status', 'new')->count()),
            Stat::make('Proccessing Order', Order::query()->where('status', 'proccessing')->count()),
            Stat::make('Shipped Order', Order::query()->where('status', 'shipped')->count()),
            Stat::make('Average Price', function () {
                $averagePrice = Order::query()->avg('grand_total');
                return 'INR ' . number_format($averagePrice, 2);
            }),
        ];
    }
}
