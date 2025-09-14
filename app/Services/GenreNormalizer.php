<?php

namespace App\Services;

use App\Models\Genre;
use Illuminate\Support\Str;

class GenreNormalizer
{
    protected array $genres;

    protected int $maxLevenshteinDistance;

    protected float $minSimilarityPercent;

    public function __construct(int $maxLevenshteinDistance = 3, float $minSimilarityPercent = 70.0)
    {
        $this->maxLevenshteinDistance = $maxLevenshteinDistance;
        $this->minSimilarityPercent = $minSimilarityPercent;
        $this->loadGenres();
    }

    protected function loadGenres(): void
    {
        $this->genres = Genre::all()->map(function (Genre $genre) {
            return [
                'id' => $genre->id,
                'name' => $genre->name,
                'name_norm' => $this->normalizeString($genre->name),
                'synonyms' => collect($genre->synonyms ?? [])->map(fn ($s) => $this->normalizeString($s))->all(),
            ];
        })->all();
    }

    protected function normalizeString(string $s): string
    {
        $s = mb_strtolower(trim($s));
        $s = Str::ascii($s); // ä -> a etc.
        // only alphanumeric + spaces
        $s = preg_replace('/[^a-z0-9 ]+/', ' ', $s);
        $s = preg_replace('/\s+/', ' ', $s);

        return trim($s);
    }

    public function match(string $rawGenre): ?Genre
    {
        $key = $this->normalizeString($rawGenre);
        if ($key === '') {
            return null;
        }

        $best = [
            'genre' => null,
            'distance' => PHP_INT_MAX,
            'similarity' => 0.0,
        ];

        foreach ($this->genres as $g) {
            // exact match
            if ($key === $g['name_norm']) {
                return Genre::find($g['id']);
            }

            // exact match with synonyms
            foreach ($g['synonyms'] as $synNorm) {
                if ($key === $synNorm) {
                    return Genre::find($g['id']);
                }
            }

            // levenshtein
            $distName = levenshtein($key, $g['name_norm']);
            if ($distName < $best['distance']) {
                $best = [
                    'genre' => Genre::find($g['id']),
                    'distance' => $distName,
                    'similarity' => 0.0,
                ];
            }

            // similar_text
            similar_text($key, $g['name_norm'], $percentName);
            if ($percentName > $best['similarity']) {
                $best = [
                    'genre' => Genre::find($g['id']),
                    'distance' => $distName,
                    'similarity' => $percentName,
                ];
            }

            // synonyms: levenshtein & similar_text
            foreach ($g['synonyms'] as $synNorm) {
                $distSyn = levenshtein($key, $synNorm);
                if ($distSyn < $best['distance']) {
                    $best = [
                        'genre' => Genre::find($g['id']),
                        'distance' => $distSyn,
                        'similarity' => 0.0,
                    ];
                }
                similar_text($key, $synNorm, $percentSyn);
                if ($percentSyn > $best['similarity']) {
                    $best = [
                        'genre' => Genre::find($g['id']),
                        'distance' => $distSyn,
                        'similarity' => $percentSyn,
                    ];
                }
            }
        }

        if ($best['genre']) {
            if ($best['distance'] <= $this->maxLevenshteinDistance) {
                return $best['genre'];
            }
            if ($best['similarity'] >= $this->minSimilarityPercent) {
                return $best['genre'];
            }
        }

        // no reaasonable match
        return null;
    }
}
