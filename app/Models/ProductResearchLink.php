<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductResearchLink extends Model
{
    protected $fillable = [
        'product_id', 'title', 'authors', 'publication_year',
        'journal', 'pubmed_id', 'url', 'sort_order',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'sort_order'       => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getPubmedUrlAttribute(): ?string
    {
        return $this->pubmed_id
            ? 'https://pubmed.ncbi.nlm.nih.gov/' . $this->pubmed_id
            : null;
    }

    /**
     * Short citation string for display: "Smith J et al. (2021)"
     */
    public function getCitationAttribute(): string
    {
        $parts = [];
        if ($this->authors) $parts[] = $this->authors;
        if ($this->publication_year) $parts[] = "({$this->publication_year})";
        return implode(' ', $parts) ?: $this->title;
    }
}
