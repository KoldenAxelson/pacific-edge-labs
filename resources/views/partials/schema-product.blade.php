@push('schema')
@php
    $productSchema = [
        '@context' => 'https://schema.org',
        '@type'    => 'Product',
        'name'     => $product->name,
        'description' => Str::limit(strip_tags($product->description), 300),
        'sku'      => $product->sku,
        'brand'    => ['@type' => 'Brand', 'name' => 'Pacific Edge Labs'],
        'url'      => $product->url,
        'offers'   => [
            '@type'         => 'Offer',
            'price'         => (string) $product->price,
            'priceCurrency' => 'USD',
            'availability'  => 'https://schema.org/InStock',
            'seller'        => ['@type' => 'Organization', 'name' => 'Pacific Edge Labs'],
        ],
    ];

    if ($product->primaryImage) {
        $productSchema['image'] = $product->primaryImage->url;
    }

    $articleSchemas = [];
    foreach ($product->researchLinks as $link) {
        $article = [
            '@context' => 'https://schema.org',
            '@type'    => 'ScholarlyArticle',
            'name'     => $link->title,
            'url'      => $link->url,
        ];
        if ($link->authors) {
            $article['author'] = $link->authors;
        }
        if ($link->publication_year) {
            $article['datePublished'] = (string) $link->publication_year;
        }
        if ($link->journal) {
            $article['publisher'] = ['@type' => 'Organization', 'name' => $link->journal];
        }
        $articleSchemas[] = $article;
    }
@endphp
<script type="application/ld+json">{!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
@foreach($articleSchemas as $articleData)
<script type="application/ld+json">{!! json_encode($articleData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
@endforeach
@endpush
