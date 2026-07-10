<?php

namespace App\Filament\Widgets;

use App\Models\Contribution;
use App\Models\ExpenseSplit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserContributionStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $userId = auth()->id();

        if (!$userId) {
            return [];
        }

        /*
         * Total que el usuario ha aportado.
         */
        $contributed = (float) Contribution::query()
            ->where('user_id', $userId)
            ->sum('amount');

        /*
         * Total asignado al usuario en todos los gastos.
         */
        $assigned = (float) ExpenseSplit::query()
            ->where('user_id', $userId)
            ->sum('amount');

        /*
         * Total realmente pagado.
         *
         * Se considera pagado cuando la división del gasto
         * ya tiene un movimiento de caja de tipo egreso.
         */
        $paid = (float) ExpenseSplit::query()
            ->where('user_id', $userId)
            ->whereHas('movements', function ($query) {
                $query->where('type', 'egreso');
            })
            ->sum('amount');

        /*
         * Cantidad de sus aportes que todavía no se ha utilizado.
         */
        $available = $contributed - $paid;

        /*
         * Gastos asignados que todavía no han sido pagados.
         */
        $pending = $assigned - $paid;

        return [
            Stat::make(
                'Total contribuido',
                'Q ' . number_format($contributed, 2)
            )
                ->description('Aportes realizados a la caja')
                ->descriptionIcon('heroicon-m-arrow-up-circle'),

            Stat::make(
                'Total pagado',
                'Q ' . number_format($paid, 2)
            )
                ->description(
                    'Pendiente por pagar: Q ' . number_format($pending, 2)
                )
                ->descriptionIcon('heroicon-m-check-circle'),

            Stat::make(
                'Saldo de mis aportes',
                'Q ' . number_format($available, 2)
            )
                ->description(
                    $available >= 0
                    ? 'Disponible de lo aportado'
                    : 'Aportes insuficientes por Q ' .
                    number_format(abs($available), 2)
                )
                ->descriptionIcon(
                    $available >= 0
                    ? 'heroicon-m-banknotes'
                    : 'heroicon-m-exclamation-triangle'
                )
                ->color($available >= 0 ? 'success' : 'danger'),
        ];
    }
}