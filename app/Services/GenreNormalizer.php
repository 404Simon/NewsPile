<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Genre;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class GenreNormalizer
{
    private array $genres;

    public function __construct(private readonly int $maxLevenshteinDistance = 3, private readonly float $minSimilarityPercent = 70.0)
    {
        $this->loadGenres();
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
            similar_text($key, (string) $g['name_norm'], $percentName);
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
                similar_text($key, (string) $synNorm, $percentSyn);
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
        Log::info("Could not match $rawGenre");

        return null;
    }

    private function loadGenres(): void
    {
        $this->genres = Genre::all()->map(fn (Genre $genre): array => [
            'id' => $genre->id,
            'name' => $genre->name,
            'name_norm' => $this->normalizeString($genre->name),
            'synonyms' => collect($genre->synonyms ?? [])->map(fn ($s): string => $this->normalizeString($s))->all(),
        ])->all();
    }

    private function normalizeString(string $s): string
    {
        $s = mb_strtolower(mb_trim($s));
        $s = Str::ascii($s); // ä -> a etc.
        // only alphanumeric + spaces
        $s = preg_replace('/[^a-z0-9 ]+/', ' ', $s);
        $s = preg_replace('/\s+/', ' ', (string) $s);

        return mb_trim($s);
    }
}
