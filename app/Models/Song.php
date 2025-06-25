<?php

namespace App\Models;

use App\Services\AudioFileService;
use App\Traits\HasUuid;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Song
 * 
 * @property string $uuid
 * @property string $name
 * @property int $duration_ms
 * @property Album $album
 * @property File $cover
 * @property string $lyrics
 * @property bool $is_explicit
 * @property int $view_count
 * @property File $file
 * @property Playlist[] $playlists
 * @property Category[] $categories
 */
class Song extends Model
{
    use HasUuid;

    const UPLOAD_PATH = 'uploads/songs/';
    
    protected $fillable = [
        'uuid',
        'name',
        'duration_ms',
        'album_id',
        'cover_id',
        'lyrics',
        'is_explicit',
        'view_count',
        'file',
    ];

    public static function boot() {
        parent::boot();

        static::deleting(function($model) {
            $filePath = storage_path($model->file);
            dd($filePath);
            if (file_exists($filePath) && !unlink($filePath)) {
                throw new Exception('Failed to unlink file');
            }
        });

        static::saving(function($model) {
            if (!$model->isDirty('file') || empty($model->file)) return;

            $originalFile = $model->getOriginal('file');
            $currentFile = $model->file;

            $sourceFilePathForConversion = Storage::disk('public')->path($currentFile);
            $convertedDirPath = Storage::disk('public')->path(static::UPLOAD_PATH.'converted/');

            if (!Storage::disk('public')->exists($convertedDirPath)) {
                Storage::disk('public')->makeDirectory($convertedDirPath);
            }

            $originalFileNameOnly = pathinfo($originalFile, PATHINFO_FILENAME);
            if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $originalFileNameOnly)) {
                Log::warning("Skipping deletion of old converted file due to unsafe original filename: {$originalFile}");
            } else {
                $oldConvertedFilePath = $convertedDirPath . $originalFileNameOnly . '.flac';
                if (file_exists($oldConvertedFilePath) && !unlink($oldConvertedFilePath)) {
                    Log::warning("Failed to delete old converted file after replacement: {$oldConvertedFilePath}");
                }
            }

            $outputFileNameForConversion = pathinfo($currentFile, PATHINFO_FILENAME).'.flac';

            $convertedFilePath = app(AudioFileService::class)->convertToFlac(
                $sourceFilePathForConversion,
                $convertedDirPath,
                $outputFileNameForConversion
            );

            if (!$convertedFilePath) {
                Log::error("Audio conversion failed for model " . get_class($model) . " (ID: " . $model->getKey() . "). See previous logs for details.");
                return;
            }
            
            $model->file = static::UPLOAD_PATH . 'converted/' . basename($convertedFilePath);
            if (file_exists($sourceFilePathForConversion) && !unlink($sourceFilePathForConversion)) {
                Log::warning("Failed to delete original raw source file after successful conversion: {$sourceFilePathForConversion}");
            }
            $model->saveQuietly();
        });
    }

    public function album() : BelongsTo {
        return $this->belongsTo(Album::class);
    }

    public function cover() : BelongsTo {
        return $this->belongsTo(File::class, 'cover_id');
    }

    public function playlists() : BelongsToMany {
        return $this->belongsToMany(Playlist::class, 'playlist_songs')
            ->withPivot('song_id')
            ->withTimestamps();
    }

    public function categories() : BelongsToMany {
        return $this->belongsToMany(Category::class, 'song_categories');
    }

    public function clientsWhoFavorited() : BelongsToMany {
        return $this->belongsToMany(
            Client::class,
            'client_favorite_songs',
            'song_id',
            'client_id'
        );
    }
}
