<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Genre;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use JsonException;

final class SyncGenres extends Command
{
    protected $signature = 'app:sync-genres {--path=database/data/genres.json : Path to the JSON file containing genres} {--force : Force delete genres not in the JSON file without asking}';

    protected $description = 'Sync genres from a JSON file into the database';

    public function handle(): int
    {
        $path = $this->option('path');

        if (! File::exists($path)) {
            $this->error("The file {$path} does not exist.");

            return Command::FAILURE;
        }

        try {
            $genres = collect(json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR));

            $this->validateGenres($genres);
            $this->syncGenres($genres);

            $this->info('Genres synchronization completed successfully.');

            return Command::SUCCESS;
        } catch (JsonException $e) {
            $this->logError('Invalid JSON format', $e);

            return Command::FAILURE;
        } catch (ValidationException $e) {
            $this->logError('Validation failed', $e);

            return Command::FAILURE;
        } catch (Exception $e) {
            $this->logError('An error occurred', $e);

            return Command::FAILURE;
        }
    }

    private function validateGenres(Collection $genres): void
    {
        if ($genres->isEmpty()) {
            throw ValidationException::withMessages(['genres' => 'The genres file cannot be empty.']);
        }

        $genres->each(function ($genre, $index): void {
            $validator = Validator::make($genre, [
                'name' => 'required|string|max:255',
                'synonyms' => 'nullable|array',
                'synonyms.*' => 'string|max:255',
            ], [
                'name.required' => "Genre at index {$index} is missing a name.",
                'synonyms.array' => "Synonyms for genre '".($genre['name'] ?? 'unknown')."' must be an array.",
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }
        });
    }

    private function syncGenres(Collection $genres): void
    {
        DB::transaction(function () use ($genres): void {
            $existingGenres = Genre::query()->select(['id', 'name'])->get()->keyBy('name');
            $stats = ['created' => 0, 'updated' => 0];

            $genreNames = $genres->map(function ($genre) use ($existingGenres, &$stats) {
                $name = $genre['name'];
                $synonyms = $genre['synonyms'] ?? [];

                if ($existingGenres->has($name)) {
                    $genre = Genre::query()->find($existingGenres[$name]->id);
                    $genre->synonyms = $synonyms;
                    $genre->save();

                    $this->line("Updated genre: {$name}");
                    $stats['updated']++;
                } else {
                    $genre = new Genre;
                    $genre->name = $name;
                    $genre->synonyms = $synonyms;
                    $genre->save();

                    $this->line("Created genre: {$name}");
                    $stats['created']++;
                }

                return $name;
            });

            // Handle genre deletion
            $shouldDelete = $this->option('force') || (! $this->option('no-interaction') && $this->confirm('Do you want to remove genres that are not in the JSON file?', true));

            if ($shouldDelete) {
                $beforeCount = Genre::query()->count();
                Genre::query()->whereNotIn('name', $genreNames)->delete();
                $afterCount = Genre::query()->count();
                $deleted = $beforeCount - $afterCount;
                $this->line("Deleted {$deleted} genres that were not in the JSON file.");
            }

            $this->info("Sync summary: {$stats['created']} created, {$stats['updated']} updated.");
        });
    }

    private function logError(string $message, Exception $e): void
    {
        $errorMsg = "{$message}: ".$e->getMessage();
        $this->error($errorMsg);
        Log::error("Genre sync failed: {$errorMsg}");
    }
}
