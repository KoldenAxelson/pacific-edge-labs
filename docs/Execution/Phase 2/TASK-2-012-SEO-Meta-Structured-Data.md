# [TASK-2-012] SEO Meta Tags & Structured Data

## Overview
Implement SEO meta tags, Open Graph tags, and schema.org `Product` + `ScholarlyArticle`
structured data on product and category pages. This is the primary SEO investment in
Phase 2.

**Phase:** 2 — Product Catalog & Pages
**Estimated time:** 2–3 hrs
**Depends on:** TASK-2-007 (product detail page must exist)
**Blocks:** Nothing

---

## Files to Create / Modify

```
resources/views/
├── partials/
│   ├── seo-meta.blade.php           ← new (reusable meta partial)
│   └── schema-product.blade.php     ← new (schema.org JSON-LD)
└── [app layout]                     ← add @stack('schema') before </body>
```

---

## Part 1: App Layout Changes

The app layout needs two slots: one for page-specific `<title>` and meta, one for JSON-LD.

In the layout `<head>`:
```blade
{{-- Page title --}}
<title>{{ $title ?? 'Pacific Edge Labs | Premium Research Peptides' }}</title>

{{-- SEO meta --}}
@yield('meta')
{{-- or if using components: --}}
{{ $meta ?? '' }}

{{-- Schema.org JSON-LD --}}
@stack('schema')
```

---

## Part 2: Reusable SEO Meta Partial (`resources/views/partials/seo-meta.blade.php`)

```blade
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
```

---

## Part 3: Product Schema (`resources/views/partials/schema-product.blade.php`)

```blade
@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "{{ $product->name }}",
  "description": "{{ Str::limit(strip_tags($product->description), 300) }}",
  "sku": "{{ $product->sku }}",
  "brand": {
    "@type": "Brand",
    "name": "Pacific Edge Labs"
  },
  "url": "{{ $product->url }}",
  "offers": {
    "@type": "Offer",
    "price": "{{ $product->price }}",
    "priceCurrency": "USD",
    "availability": "https://schema.org/InStock",
    "seller": {
      "@type": "Organization",
      "name": "Pacific Edge Labs"
    }
  }
  @if($product->primaryImage)
  ,"image": "{{ $product->primaryImage->url }}"
  @endif
}
</script>

@foreach($product->researchLinks as $link)
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ScholarlyArticle",
  "name": "{{ $link->title }}",
  "url": "{{ $link->url }}"
  @if($link->authors)
  ,"author": "{{ $link->authors }}"
  @endif
  @if($link->publication_year)
  ,"datePublished": "{{ $link->publication_year }}"
  @endif
  @if($link->journal)
  ,"publisher": {
    "@type": "Organization",
    "name": "{{ $link->journal }}"
  }
  @endif
}
</script>
@endforeach
@endpush
```

---

## Part 4: Integrate into Product Detail Page

In `resources/views/products/show.blade.php`, add inside the layout:

```blade
{{-- In the <head> slot / meta section --}}
@include('partials.seo-meta', [
    'metaTitle'    => $product->effective_meta_title,
    'metaDesc'     => $product->effective_meta_description,
    'canonicalUrl' => route('products.show', $product->slug),
    'ogType'       => 'product',
    'ogImage'      => $product->primaryImage?->url ?? asset('images/og-default.png'),
])

{{-- Schema --}}
@include('partials.schema-product', ['product' => $product])
```

---

## Part 5: Category Page SEO

In `resources/views/categories/show.blade.php`:

```blade
@include('partials.seo-meta', [
    'metaTitle'    => ($category->hero_title ?? $category->name) . ' | Pacific Edge Labs',
    'metaDesc'     => Str::limit(strip_tags($category->description ?? ''), 155),
    'canonicalUrl' => route('categories.show', $category->slug),
])
```

---

## Part 6: Products Index SEO

In `resources/views/products/index.blade.php`:

```blade
@include('partials.seo-meta', [
    'metaTitle'    => 'Research Peptides for Sale | Pacific Edge Labs',
    'metaDesc'     => 'Browse Pacific Edge Labs\' complete catalog of USA-tested, purity-verified research peptides. GLP-1 agonists, recovery peptides, nootropics, and more.',
    'canonicalUrl' => route('products.index'),
])
```

---

## Verification

After implementation, verify with:
- Chrome DevTools → View Source → search `application/ld+json`
- Google Rich Results Test: https://search.google.com/test/rich-results
- Open Graph debugger: https://developers.facebook.com/tools/debug/

---

## Acceptance Criteria
- [ ] Every product page has a unique `<title>` matching `$product->effective_meta_title`
- [ ] Every product page has a `<meta name="description">` ≤ 155 chars
- [ ] `<link rel="canonical">` present on product and category pages
- [ ] Open Graph `og:title`, `og:description`, `og:url`, `og:image` present on product pages
- [ ] `application/ld+json` Product schema renders correctly on product pages
- [ ] `application/ld+json` ScholarlyArticle renders for each research link on Semaglutide
- [ ] Google Rich Results Test passes for at least one product page
- [ ] Category pages have correct title and description meta
- [ ] Products index page has descriptive meta (not auto-generated)
- [ ] No duplicate `<title>` tags in page source
