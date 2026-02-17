@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "{{ e($product->name) }}",
  "description": "{{ e(Str::limit(strip_tags($product->description), 300)) }}",
  "sku": "{{ e($product->sku) }}",
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
  "name": "{{ e($link->title) }}",
  "url": "{{ e($link->url) }}"
  @if($link->authors)
  ,"author": "{{ e($link->authors) }}"
  @endif
  @if($link->publication_year)
  ,"datePublished": "{{ $link->publication_year }}"
  @endif
  @if($link->journal)
  ,"publisher": {
    "@type": "Organization",
    "name": "{{ e($link->journal) }}"
  }
  @endif
}
</script>
@endforeach
@endpush
