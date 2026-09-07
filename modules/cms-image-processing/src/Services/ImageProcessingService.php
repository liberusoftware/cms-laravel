<?php

declare(strict_types=1);

namespace Liberu\Cms\ImageProcessing\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ImageProcessing\Models\ImageDerivative;
use Liberu\Cms\ImageProcessing\Models\ProcessingProfile;

final class ImageProcessingService
{
    public function profile(string $key, string $format = 'webp', int $quality = 82, ?int $width = null, ?int $height = null, string $fit = 'cover', ?int $teamId = null): ProcessingProfile
    {
        $this->validKey($key, 'key');
        if (! in_array(strtolower($format), ['jpg', 'jpeg', 'png', 'webp', 'avif'], true)) {
            throw ValidationException::withMessages(['format' => 'Unsupported image format.']);
        } if ($quality < 1 || $quality > 100) {
            throw ValidationException::withMessages(['quality' => 'Quality must be between 1 and 100.']);
        } if (($width !== null && ($width < 1 || $width > 10000)) || ($height !== null && ($height < 1 || $height > 10000))) {
            throw ValidationException::withMessages(['dimensions' => 'Image dimensions are outside the supported range.']);
        } if (! in_array($fit, ['cover', 'contain', 'crop', 'inside'], true)) {
            throw ValidationException::withMessages(['fit' => 'Unsupported image fit.']);
        }

        return ProcessingProfile::query()->updateOrCreate(['team_id' => $teamId, 'key' => $key], ['public_id' => (string) Str::uuid(), 'format' => strtolower($format), 'quality' => $quality, 'width' => $width, 'height' => $height, 'fit' => $fit]);
    }

    public function validate(string $mime, int $bytes, ?int $width = null, ?int $height = null): void
    {
        if (! in_array(strtolower($mime), ['image/jpeg', 'image/png', 'image/webp', 'image/avif'], true)) {
            throw ValidationException::withMessages(['mime' => 'The image format is not supported.']);
        } if ($bytes < 1 || $bytes > 52428800) {
            throw ValidationException::withMessages(['bytes' => 'The image must be between 1 byte and 50 MB.']);
        } if (($width !== null && $width < 1) || ($height !== null && $height < 1)) {
            throw ValidationException::withMessages(['dimensions' => 'Image dimensions must be positive.']);
        }
    }

    /** @param array<string, mixed> $metadata */
    public function derivative(ProcessingProfile $profile, string $assetKey, string $checksum, array $metadata = [], ?int $teamId = null): ImageDerivative
    {
        $this->validKey($assetKey, 'asset_key');
        $this->validKey($checksum, 'source_checksum');
        if ($profile->team_id !== $teamId) {
            throw ValidationException::withMessages(['team_id' => 'The processing profile belongs to another tenant.']);
        }

        return ImageDerivative::query()->updateOrCreate(['profile_id' => $profile->id, 'asset_key' => $assetKey, 'source_checksum' => $checksum], ['team_id' => $teamId, 'public_id' => (string) Str::uuid(), 'path' => 'derivatives/'.$profile->key.'/'.$checksum.'.'.$profile->format, 'status' => 'ready', 'metadata' => $metadata]);
    }

    public function cdnUrl(ImageDerivative $derivative, string $baseUrl = ''): string
    {
        if (! filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages(['base_url' => 'A valid CDN base URL is required.']);
        }

        return rtrim($baseUrl, '/').'/'.ltrim($derivative->path, '/');
    }

    private function validKey(string $value, string $field): void
    {
        if (trim($value) === '' || strlen($value) > 500 || str_contains($value, '..') || str_contains($value, "\0")) {
            throw ValidationException::withMessages([$field => 'The image identifier is invalid.']);
        }
    }
}
