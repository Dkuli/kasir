<div class="flex items-center justify-between flex-shrink-0 px-3">
    <!-- Logo -->
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2">
        <!-- Display the logo -->
        <x-application-logo class="w-10 h-auto" />
        <x-application-logoOpen x-show="isSidebarOpen" class="w-20 h-10" />

        <!-- Display the application name only when the sidebar is open -->


        <!-- Visually hidden for accessibility -->
        <span class="sr-only">Dashboard</span>
    </a>

    <!-- Toggle button -->
    <x-button
        type="button"
        icon-only
        sr-text="Toggle sidebar"
        variant="secondary"
        x-show="isSidebarOpen || isSidebarHovered"
        x-on:click="isSidebarOpen = !isSidebarOpen"
    >
        <x-icons.menu-fold-right
            x-show="!isSidebarOpen"
            aria-hidden="true"
            class="hidden w-6 h-6 lg:block"
        />

        <x-icons.menu-fold-left
            x-show="isSidebarOpen"
            aria-hidden="true"
            class="hidden w-6 h-6 lg:block"
        />

        <x-heroicon-o-x aria-hidden="true" class="w-6 h-6 lg:hidden" />
    </x-button>
</div>
