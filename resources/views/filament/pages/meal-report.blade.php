<x-filament-panels::page>
    <form wire:submit.prevent>
        {{ $this->form }}
    </form>

    <div class="mt-6">
        <x-filament::button wire:click="exportPdf" icon="heroicon-o-arrow-down-tray">
            Exportar PDF
        </x-filament::button>
    </div>

    @php
        $weekStart = \Carbon\Carbon::parse($this->data['week_start'] ?? now())
            ->startOfWeek(\Carbon\Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->addDays(4);
    @endphp

    <div class="mt-6 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <h3 class="font-bold text-lg">
            Semana del {{ $weekStart->format('d/m/Y') }} al {{ $weekEnd->format('d/m/Y') }}
        </h3>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-800">
                    <th class="px-4 py-2 text-left">Catálogo</th>
                    <th class="px-4 py-2 text-left">Grado</th>
                    <th class="px-4 py-2 text-left">Arma</th>
                    <th class="px-4 py-2 text-left">Nombre</th>
                    <th class="px-4 py-2 text-center">Desayuno</th>
                    <th class="px-4 py-2 text-center">Almuerzo</th>
                    <th class="px-4 py-2 text-center">Cena</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($this->getRecords() as $record)
                    @php
                        $meal = $record->mealAttendances->first();
                    @endphp

                    <tr class="border-t border-gray-200 dark:border-gray-700">
                        <td class="px-4 py-2">{{ $record?->catalog_number ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $record?->grade?->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $record?->weaponBranch?->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $record?->name }}</td>
                        <td class="px-4 py-2 text-center">{{ $meal?->breakfast ? 'Sí' : 'No' }}</td>
                        <td class="px-4 py-2 text-center">{{ $meal?->lunch ? 'Sí' : 'No' }}</td>
                        <td class="px-4 py-2 text-center">{{ $meal?->dinner ? 'Sí' : 'No' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                            No hay usuarios para mostrar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>