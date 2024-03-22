<?php

declare(strict_types=1);

namespace Dompdf\Image;

use Dompdf\Dompdf;
use Exception;
use InvalidArgumentException;
use RuntimeException;

interface ImageResolverInterface
{
    public function resolve(string $url, string $protocol, string $host, string $basePath, Dompdf $dompdf): array;
}

final class ImageException extends Exception
{
}

final class ImageNotFoundException extends ImageException
{
}

final class InvalidImageException extends ImageException
{
}

final class Image
{
    private string $url;
    private string $localPath;
    private string $extension;
    private int $width;
    private int $height;
    private string $type;

    public function __construct(string $url, string $localPath, string $extension, int $width, int $height, string $type)
    {
        $this->url = $url;
        $this->localPath = $localPath;
        $this->extension = $extension;
        $this->width = $width;
        $this->height = $height;
        $this->type = $type;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getLocalPath(): string
    {
        return $this->localPath;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getType(): string
    {
        return $this->type;
    }
}

final class ImageCache
{
    private array $cache = [];

    public function add(string $url, string $localPath): void
    {
        $this->cache[$url] = $localPath;
    }

    public function get(string $url): ?string
    {
        return $this->cache[$url] ?? null;
    }

    public function clear(): void
    {
        $this->cache = [];
    }
}

final class ImageDataUri
{
    public static function parse(string $dataUri): array
    {
        $pattern = '/data:image\/(?P<type>[a-z]+);base64,(?P<data>[a-zA-Z0-9+\/]+=*)/';
        $matches = [];

        if (preg_match($pattern, $dataUri, $matches)) {
            return [
                'type' => $matches['type'],
                'data' => $matches['data'],
            ];
        }

        throw new InvalidArgumentException('Invalid data URI.');
    }
}

final class ImageHttpContext
{
    private array $options = [];

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}

final class ImageFile
{
    public static function isReadable(string $filePath): bool
    {
        return is_readable($filePath) && filesize($filePath) > 0;
    }

    public static function getSize(string $filePath): array
    {
        $size = getimagesize($filePath);

        if ($size === false) {
            throw new RuntimeException('Unable to get image size.');
        }

        return [
            'width' => $size[0],
            'height' => $size[1],
            'type' => ImageType::fromMimeType($size['mime']),
        ];
    }

    public static function getMimeType(string $filePath): string
    {
        $size = getimagesize($filePath);

        if ($size === false) {
            throw new RuntimeException('Unable to get image size.');
        }

        return $size['mime'];
    }
}

final class ImageType
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromMimeType(string $mimeType): self
    {
        $parts = explode('/', $mimeType);

        if (count($parts) !== 2) {
            throw new InvalidArgumentException('Invalid MIME type.');
        }

        $type = strtolower($parts[1]);

        switch ($type) {
            case 'gif':
                return new self('gif');
            case 'png':
                return new self('png');
            case 'jpeg':
            case 'jpg':
                return new self('jpeg');
            default:
                throw new InvalidArgumentException('Unsupported image type.');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

final class ImageSize
{
    private int $width;
    private int $height;

    public function __construct(int $width, int $height)
    {
