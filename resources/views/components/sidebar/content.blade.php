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

    <!-- Transaksi Dropdown -->
    <x-sidebar.dropdown
        title="Transaksi"
        :active="Str::startsWith(request()->route()->uri(), 'transaksi')"
    >
        <x-slot name="icon">
            <x-heroicon-o-cash class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>

        <x-sidebar.sublink
            title="Riwayat Transaksi"
            href="/transactions/history"
            :active="false"
        />
        <x-sidebar.sublink
            title="Buat Transaksi Baru"
            href="/transactions"
            :active="false"
        />
    </x-sidebar.dropdown>

    <!-- Master Dropdown -->
    <x-sidebar.dropdown
        title="Master"
        :active="Str::startsWith(request()->route()->uri(), 'master')"
    >
        <x-slot name="icon">
            <x-heroicon-o-archive class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>

        <x-sidebar.sublink
            title="Data Produk"
            href="/products"
            :active="false"
        />
        <x-sidebar.sublink
            title="Data Kategori"
            href="/categories"
            :active="false"
        />
        <x-sidebar.sublink
            title="Data Supplier"
            href="#"
            :active="false"
        />
    </x-sidebar.dropdown>

    <!-- Data Data User Dropdown -->
    <x-sidebar.dropdown
        title="Data Data User"
        :active="Str::startsWith(request()->route()->uri(), 'data-user')"
    >
        <x-slot name="icon">
            <x-heroicon-o-user-group class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>

        <x-sidebar.sublink
            title="Daftar Pengguna"
            href="account"
            :active="false"
        />
        <x-sidebar.sublink
            title="Tambah Pengguna"
            href="#"
            :active="false"
        />
        <x-sidebar.sublink
            title="Hak Akses"
            href="#"
            :active="false"
        />
    </x-sidebar.dropdown>

    <!-- Report Dropdown -->
    <x-sidebar.dropdown
        title="Laporan"
        :active="Str::startsWith(request()->route()->uri(), 'report')"
    >
        <x-slot name="icon">
            <x-heroicon-o-document-report class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>

        <x-sidebar.sublink
            title="Laporan Transaksi"
            href="/reports/transactions"
            :active="false"
        />
        <x-sidebar.sublink
            title="Laporan Produk"
            href="/reports/products"
            :active="false"
        />
    </x-sidebar.dropdown>

</x-perfect-scrollbar>
