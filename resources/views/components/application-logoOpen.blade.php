<svg
    viewBox="0 0 150 64"
    xmlns="http://www.w3.org/2000/svg"
    {{ $attributes }}
>
    <!-- Definisi gaya dengan font custom -->
    <style>
        @font-face {
            font-family: 'MonainnRegular';
            src: url('{{ asset('MonainnRegular-RpWKo.otf') }}') format('opentype');
        }

        .custom-text {
            font-family: 'MonainnRegular';
            font-size: 50px;
            fill: #000000;
        }
    </style>

    <!-- Background rounded rectangle -->
    <rect x="0" y="0" width="150" height="64" rx="15" fill="url(#iconGradientKasir)" />

    <!-- Text "cashier" in the center with custom font -->
    <text class="custom-text" x="50%" y="60%" dominant-baseline="middle" text-anchor="middle">
        Cashier
    </text>

</svg>
