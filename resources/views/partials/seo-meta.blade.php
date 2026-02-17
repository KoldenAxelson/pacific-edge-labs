@php
    $canonicalUrl = $canonicalUrl ?? request()->url();
    $metaTitle    = $metaTitle ?? config('app.name');
    $metaDesc     = $metaDesc ?? '';
    $ogImage      = $ogImage ?? asset('images/og-default.png');
@endphp

{{-- Standard Meta --}}
<meta name="description" content="{{ $metaDesc }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

{{-- Open Graph --}}
<meta property="og:type" content="{{ $ogType ?? 'website' }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDesc }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:site_name" content="Pacific Edge Labs">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDesc }}">
<meta name="twitter:image" content="{{ $ogImage }}">
