<x-filament-panels::page>
    <x-filament-panels::form wire:submit="exportPdf">
        {{ $this->form }}

        <div class="flex justify-end gap-x-3">
            <x-filament::button type="submit" icon="heroicon-o-document-arrow-down">
                Generar Calendario PDF
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>