@php
    // Prefer uploaded logo; fall back to a small inline SVG so favicon never 404s.
    $logoPath = $systemSettings['logo_path'] ?? null;

    if ($logoPath && !str_starts_with($logoPath, ['http://', 'https://'])) {
        // Normalize storage-relative paths to absolute URLs
        $logoPath = asset(ltrim($logoPath, '/'));
    }

    $fallbackFavicon = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='12' fill='%23d47311'/%3E%3Cpath d='M26 14c-7.2 0-12 7.4-12 18s4.8 18 12 18 12-7.4 12-18-4.8-18-12-18Zm15 2c2.2.5 3.7 1.9 4.4 4.5.6 2.1.6 5 .6 8.5s0 6.4-.6 8.5c-.7 2.6-2.2 4-4.4 4.5-1.6.4-3.2-.2-4.3-1.5 2.6-3.3 4.1-7.8 4.1-13s-1.5-9.7-4.1-13c1.1-1.3 2.7-1.9 4.3-1.5Z' fill='%23fff' fill-opacity='0.9'/%3E%3C/svg%3E";
    $faviconUrl = $logoPath ?: $fallbackFavicon;
@endphp

<link rel="icon" href="{{ $faviconUrl }}" sizes="any">
<link rel="apple-touch-icon" href="{{ $faviconUrl }}">
