<x-filament-panels::layout.base :livewire="$this ?? null">
    <div class="fi-layout flex min-h-screen w-full overflow-x-clip">
        <x-filament-panels::sidebar />

        <div class="fi-main-ctn w-screen flex-1 flex-col opacity-100">
            <x-filament-panels::topbar />

            <main class="fi-main mx-auto h-full w-full px-4 md:px-6 lg:px-8">
                <div class="fi-page py-8">
                    @include('filament.resources.clients.pages.view-client-premium-content', [
                        'client' => $client,
                        'record' => $record,
                    ])
                </div>
            </main>
        </div>
    </div>
</x-filament-panels::layout.base>
