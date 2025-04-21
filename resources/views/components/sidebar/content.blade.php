<x-perfect-scrollbar
    as="nav"
    aria-label="main"
    class="flex flex-col flex-1 gap-4 px-3"
>

    <x-sidebar.link
        title="Dashboard"
        href="{{ route('dashboard') }}"
        :isActive="request()->routeIs('dashboard')"
    >
        <x-slot name="icon">
            <x-icons.dashboard class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>
    </x-sidebar.link>

    <!-- Transaksi Dropdown - Available to all users -->
    <x-sidebar.dropdown
        title="Transaksi"
        :active="request()->routeIs('transactions.*')"
    >
        <x-slot name="icon">
            <x-heroicon-o-cash class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>

        <x-sidebar.sublink
            title="Riwayat Transaksi"
            href="{{ route('transactions.history') }}"
            :active="request()->routeIs('transactions.history')"
        />
        <x-sidebar.sublink
            title="Buat Transaksi Baru"
            href="{{ route('transactions.index') }}"
            :active="request()->routeIs('transactions.index')"
        />
    </x-sidebar.dropdown>

    <!-- Master Dropdown - Only for admin and manager -->
    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'manager')
    <x-sidebar.dropdown
        title="Master"
        :active="request()->routeIs('products.*') || request()->routeIs('categories.*')"
    >
        <x-slot name="icon">
            <x-heroicon-o-archive class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>

        <x-sidebar.sublink
            title="Data Produk"
            href="{{ route('products.index') }}"
            :active="request()->routeIs('products.*')"
        />
        <x-sidebar.sublink
            title="Data Kategori"
            href="{{ route('categories.index') }}"
            :active="request()->routeIs('categories.*')"
        />
    </x-sidebar.dropdown>
    @endif

    <!-- Data User Dropdown - Only for admin -->
    @if(auth()->user()->role == 'admin')
    <x-sidebar.dropdown
        title="Data User"
        :active="request()->routeIs('users.*')"
    >
        <x-slot name="icon">
            <x-heroicon-o-user-group class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>

        <x-sidebar.sublink
            title="Daftar Pengguna"
            href="{{ route('users.index') }}"
            :active="request()->routeIs('users.index')"
        />
    </x-sidebar.dropdown>
    @endif

    <!-- Report Dropdown - Only for admin and manager -->
    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'manager')
    <x-sidebar.dropdown
        title="Laporan"
        :active="request()->routeIs('reports.*')"
    >
        <x-slot name="icon">
            <x-heroicon-o-document-report class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>

        <x-sidebar.sublink
            title="Laporan Transaksi"
            href="{{ route('reports.index') }}"
            :active="request()->routeIs('reports.*')"
        />
    </x-sidebar.dropdown>
    @endif

</x-perfect-scrollbar>
