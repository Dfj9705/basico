<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between gap-x-4">
            <div class="flex items-center gap-x-4">
                @if($todayShift['has_shift'])
                    <!-- Icono de turno activo -->
                    <div
                        class="p-3 bg-success-100 dark:bg-success-500/10 text-success-600 dark:text-success-400 rounded-lg">
                        <x-filament::icon icon="heroicon-o-clock" class="h-7 w-7" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold tracking-tight text-gray-950 dark:text-white">
                            ¡Tienes un turno asignado hoy!
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Estás asignado al turno: <span
                                class="font-semibold text-gray-800 dark:text-gray-200">{{ $todayShift['name'] }}</span>
                            ({{ $todayShift['frequency'] }})
                        </p>
                        @if($todayShift['start_time'] && $todayShift['end_time'])
                            <p class="text-xs text-primary-600 dark:text-primary-400 mt-1 font-medium">
                                Horario: {{ $todayShift['start_time'] }} - {{ $todayShift['end_time'] }} hrs
                            </p>
                        @endif
                        @if($todayShift['notes'])
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 italic">
                                Nota: "{{ $todayShift['notes'] }}"
                            </p>
                        @endif
                    </div>
                @else
                    <!-- Icono de libre / sin turno -->
                    <div class="p-3 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-lg">
                        <x-filament::icon icon="heroicon-o-calendar" class="h-7 w-7" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold tracking-tight text-gray-950 dark:text-white">
                            No tienes servicios programados para hoy
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Disfruta de tu jornada o día libre. No tienes ningun servicio asignado el día de hoy.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Enlace rápido para ver más detalles -->
            <div>
                <a href="{{ route('filament.admin.pages.monthly-shift-report') }}"
                    class="text-sm font-semibold text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300 flex items-center gap-x-1">
                    Ver calendario completo
                    <x-filament::icon icon="heroicon-m-arrow-trending-up" class="h-4 w-4" />
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>