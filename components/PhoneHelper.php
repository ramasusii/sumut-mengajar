<?php
namespace app\components;

final class PhoneHelper
{
    public static function normalizeIndonesia(?string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string)$value);
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        if (!str_starts_with($digits, '62')) {
            return null;
        }

        if (strlen($digits) < 10 || strlen($digits) > 15) {
            return null;
        }

        return $digits;
    }

    public static function variants(?string $value): array
    {
        $normalized = self::normalizeIndonesia($value);
        if (!$normalized) {
            return [];
        }

        $local = '0' . substr($normalized, 2);
        return array_values(array_unique([$normalized, '+' . $normalized, $local]));
    }

    public static function display(?string $value): string
    {
        $normalized = self::normalizeIndonesia($value);
        return $normalized ? '+' . $normalized : (string)$value;
    }
}
