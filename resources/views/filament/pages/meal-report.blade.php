<x-filament-panels::page>
    <form wire:submit.prevent>
        {{ $this->form }}
    </form>

    <div class="mt-6">
        <x-filament::button wire:click="exportPdf" icon="heroicon-o-arrow-down-tray">
            Exportar PDF
        </x-filament::button>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-800">
                    <th class="px-4 py-2 text-left">Catálogo</th>
                    <th class="px-4 py-2 text-left">Grado</th>
                    <th class="px-4 py-2 text-left">Arma</th>
                    <th class="px-4 py-2 text-left">Nombre</th>
                    <th class="px-4 py-2 text-center">Lunes</th>
                    <th class="px-4 py-2 text-center">Martes</th>
                    <th class="px-4 py-2 text-center">Miércoles</th>
                    <th class="px-4 py-2 text-center">Jueves</th>
                    <th class="px-4 py-2 text-center">Viernes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->getRecords() as $record)
                    <tr class="border-t border-gray-200 dark:border-gray-700">
                        <td class="px-4 py-2">{{ $record?->catalog_number ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $record?->grade?->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $record?->weaponBranch?->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $record?->name }}</td>

                        @php
                            $weekStart = \Carbon\Carbon::parse($this->data['week_start'] ?? now())->startOfWeek(\Carbon\Carbon::MONDAY);
                        @endphp

                        @for ($i = 0; $i < 5; $i++)
                            @php
                                $currentDate = $weekStart->copy()->addDays($i);
                            @endphp

                            <h3 class="mt-6 mb-2 font-bold">
                                {{ ucfirst($currentDate->locale('es')->translatedFormat('l')) }}
                                {{ $currentDate->format('d/m/Y') }}
                            </h3>

                            <table class="w-full text-sm border">
                                <thead>
                                    <tr>
                                        <th>Catálogo</th>
                                        <th>Grado</th>
                                        <th>Arma</th>
                                        <th>Nombre</th>
                                        <th>Desayuno</th>
                                        <th>Almuerzo</th>
                                        <th>Cena</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($this->getRecords() as $record)
                                        @php
                                            $meal = $record->mealAttendances->first(function ($item) use ($currentDate) {
                                                return \Carbon\Carbon::parse($item->date)->toDateString()
                                                    === $currentDate->toDateString();
                                            });
                                        @endphp

                                        <tr>
                                            <td>{{ $record?->catalog_number ?? '-' }}</td>
                                            <td>{{ $record?->grade?->name ?? '-' }}</td>
                                            <td>{{ $record?->weaponBranch?->name ?? '-' }}</td>
                                            <td>{{ $record?->name }}</td>
                                            <td class="text-center">{{ $meal?->breakfast ? 'Sí' : 'No' }}</td>
                                            <td class="text-center">{{ $meal?->lunch ? 'Sí' : 'No' }}</td>
                                            <td class="text-center">{{ $meal?->dinner ? 'Sí' : 'No' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                            No hay usuarios para mostrar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>