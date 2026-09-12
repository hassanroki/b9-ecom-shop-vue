<?php

namespace App\Services;

use App\Models\HeroSlide;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HeroSlideImageService
{
    /**
     * Resolve a stored image value to a public URL.
     */
    public function resolveUrl(?string $image): ?string
    {
        if (! filled($image)) {
            return null;
        }

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        return Storage::url($image);
    }

    /**
     * Determine whether the image value refers to an uploaded file.
     */
    public function isUploadedPath(?string $image): bool
    {
        return filled($image) && ! filter_var($image, FILTER_VALIDATE_URL);
    }

    /**
     * @return array{
     *     image: string|null,
     *     image_source: 'url'|'upload',
     *     image_url: string
     * }
     */
    public function payloadForHeroSlide(HeroSlide $heroSlide): array
    {
        $isUploaded = $this->isUploadedPath($heroSlide->image);

        return [
            'image' => $this->resolveUrl($heroSlide->image),
            'image_source' => $isUploaded ? 'upload' : 'url',
            'image_url' => $isUploaded ? '' : ($heroSlide->image ?? ''),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function prepareForStorage(array $data, ?UploadedFile $uploadedFile = null, ?HeroSlide $existing = null): array
    {
        $source = $data['image_source'] ?? 'url';
        unset($data['image_source']);

        if ($source === 'upload') {
            if ($uploadedFile !== null) {
                $this->deleteIfUploaded($existing?->image);
                $data['image'] = $uploadedFile->store('hero-slides', 'public');
            } elseif ($existing !== null) {
                unset($data['image']);
            } else {
                $data['image'] = null;
            }

            return $data;
        }

        if ($existing !== null && $this->isUploadedPath($existing->image)) {
            $this->deleteIfUploaded($existing->image);
        }

        $image = $data['image'] ?? null;
        $data['image'] = filled($image) ? $image : null;

        return $data;
    }

    /**
     * Delete an uploaded hero slide image from storage when no longer needed.
     */
    public function deleteIfUploaded(?string $image): void
    {
        if (! $this->isUploadedPath($image)) {
            return;
        }

        Storage::disk('public')->delete($image);
    }
}
