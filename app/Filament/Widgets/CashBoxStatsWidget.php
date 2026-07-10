<?php

namespace App\Filament\Widgets;

use App\Models\CashBoxMovement;
use App\Models\Force;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashBoxStatsWidget extends StatsOverviewWidget
{


    protected function getStats(): array
    {
        $user = auth()->user();

        if (!$user->hasRole('Administrador')) {
            $forceId = $user->weaponBranch?->force_id;

            if (!$forceId) {
                return [];
            }

            return $this->statsForForce($forceId);
        }

        return Force::query()
            ->where('is_active', true)
            ->get()
            ->map(function (Force $force): Stat {
                return Stat::make(
                    $force->name,
                    'Q ' . number_format(
                        CashBoxMovement::getTotalByForce($force->id),
                        2
                    )
                )
                    ->description('Saldo disponible')
                    ->descriptionIcon('heroicon-m-banknotes');
            })
            ->all();
    }

    private function statsForForce(int $forceId): array
    {
        $movements = CashBoxMovement::query()
            ->where('force_id', $forceId);

        $income = (clone $movements)
            ->whereIn('type', ['ingreso'])
            ->sum('quantity');

        $expenses = (clone $movements)
            ->where('type', 'egreso')
            ->sum('quantity');

        $transferences = (clone $movements)
            ->where('type', 'transferencia')
            ->sum('quantity');

        $balance = $income + $transferences - $expenses;

        return [
            Stat::make(
                'Saldo disponible',
                'Q ' . number_format($balance, 2)
            )
                ->description('Caja de tu fuerza')
                ->descriptionIcon('heroicon-m-banknotes'),

            Stat::make(
                'Ingresos',
                'Q ' . number_format($income, 2)
            )
                ->description('Aportes y transferencias')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),

            Stat::make(
                'Egresos',
                'Q ' . number_format($expenses, 2)
            )
                ->description('Gastos y transferencias enviadas')
                ->descriptionIcon('heroicon-m-arrow-trending-down'),
        ];
    }
}
