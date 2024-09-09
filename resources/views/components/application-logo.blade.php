<svg
    viewBox="0 0 64 64"
    xmlns="http://www.w3.org/2000/svg"
    {{ $attributes }}
>
    <!-- Gradient definition -->
    <defs>
        <linearGradient id="iconGradientClosed" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#00F0FF;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#4C40F7;stop-opacity:1" />
        </linearGradient>
    </defs>

    <!-- Icon base shape (rounded square) -->
    <rect x="7" y="7" width="50" height="50" rx="15" fill="url(#iconGradientClosed)" />

    <!-- Inner elements (abstract transaction symbol) -->
    <path d="M20,32 L44,32 M32,20 L32,44" stroke="#FFFFFF" stroke-width="4" stroke-linecap="round" />
    <circle cx="32" cy="32" r="8" fill="#00F0FF" />

    <!-- Small accent circles for dynamism -->
    <circle cx="24" cy="24" r="2" fill="#FFFFFF" />
    <circle cx="40" cy="24" r="2" fill="#FFFFFF" />
    <circle cx="24" cy="40" r="2" fill="#FFFFFF" />
    <circle cx="40" cy="40" r="2" fill="#FFFFFF" />
</svg>
