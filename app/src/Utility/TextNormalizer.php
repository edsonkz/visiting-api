<?php

declare(strict_types=1);

namespace App\Utility;

class TextNormalizer
{
    public static function normalize(string $text): string
    {
        // Remove whitespaces (NBSP, etc.)
        $text = preg_replace('/[\x{00A0}\x{1680}\x{180E}\x{2000}-\x{200A}\x{202F}\x{205F}\x{3000}\x{FEFF}]/u', ' ', $text);

        // Remove double spaces and trim the string
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}
