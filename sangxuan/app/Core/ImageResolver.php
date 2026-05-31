<?php

/**
 * ImageResolver
 * Ưu tiên ảnh local trong assets/images/books/ nếu tồn tại.
 */
class ImageResolver
{
    const LOCAL_DIR = 'assets/images/books/';

    public static function resolve(array $row, string $docRoot): array
    {
        if (empty($row['image_url'])) {
            return $row;
        }

        $url      = $row['image_url'];
        $filename = basename($url);
        $localRel = self::LOCAL_DIR . $filename;

        // Hỗ trợ cả Windows (\) lẫn Linux (/)
        $localAbs = rtrim($docRoot, '/\\') . DIRECTORY_SEPARATOR
                  . str_replace('/', DIRECTORY_SEPARATOR, $localRel);

        if (file_exists($localAbs)) {
            $row['image_url'] = $localRel;
        }

        return $row;
    }

    public static function resolveMany(array $rows, string $docRoot): array
    {
        return array_map(fn($r) => self::resolve($r, $docRoot), $rows);
    }
}
