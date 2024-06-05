<?php

namespace Flavorly\LaravelHelpers\Macros;

use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Flavorly\LaravelHelpers\Contracts\RegistersMacros;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class StrMacros implements RegistersMacros
{
    public static function register(): void
    {
        self::normalizeCharacters();
        self::acronimize();
        self::username();
        self::money();
        self::spin();
    }

    public static function username(): void
    {
        Str::macro('username', function (string $string): string {
            $parsed = $string;
            if (Str::contains($string, '@')) {
                $parsed = strstr($string, '@', true);
            }

            if (! $parsed) {
                return $string;
            }

            $parsed = Str::slug($string);

            $counter = 1;
            $username = $parsed;
            /**
             * @var Authenticatable|Model $model
             */
            $model = config('auth.providers.users.model', config('auth.model', 'App\User'));
            while ($model::query()->where('username', $username)->exists()) {
                $username = $parsed.$counter;
                $counter++;
            }

            return $username;
        });
    }

    public static function spin(): void
    {
        Str::macro('spin', function (string $string): string {

            $string = str_replace('[[', '{', $string);
            $string = str_replace(']]', '}', $string);

            preg_match('#{(.+?)}#is', $string, $matches);
            if (empty($matches)) {
                return $string;
            }

            $token = $matches[1];

            if (str_contains($token, '{')) {
                $token = substr($token, strrpos($token, '{') + 1);
            }

            $parts = explode('|', $token);
            $string = preg_replace('+{'.preg_quote($token).'}+is', $parts[array_rand($parts)], $string, 1);

            // @phpstan-ignore-next-line
            return Str::spin($string);
        });
    }

    public static function money(): void
    {
        Str::macro('money', function (string $money, ?string $currency = null): string {
            if (! class_exists(\Brick\Money\Money::class)) {
                return $money;
            }
            /** @var Authenticatable $user */
            $user = auth()->user();
            $currency = $currency ?? $user?->wallet_currency ?? config('app.default_currency');

            return Money::of(
                $money,
                // @phpstan-ignore-next-line
                config('app.default_currency', $currency),
                roundingMode: RoundingMode::UP
            )->formatTo($user?->locale ?? config('app.locale'));
        });
    }

    public static function acronimize(): void
    {
        Str::macro('acronimize', function (string $string): string {
            $name = mb_convert_encoding($string, 'UTF-8', 'auto');
            // @phpstan-ignore-next-line
            $name = Str::normalizeCharacters($name);

            $acronym = '';
            $words = explode(' ', $name, 2);

            if (empty(array_filter($words))) {
                return $name;
            }

            foreach ($words as $w) {
                // Get First letter of each word
                if (isset($w[0])) {
                    $acronym .= $w[0];
                }
            }

            if (empty($acronym)) {
                return 'IG';
            }

            return mb_convert_encoding($acronym, 'UTF-8', 'auto');
        });
    }

    /**
     * Normalize non-ascii characters
     */
    public static function normalizeCharacters(): void
    {
        Str::macro('normalizeCharacters', function (string $string): string {
            if (! preg_match('/[\x80-\xff]/', $string)) {
                return $string;
            }
            $chars = [
                // Decompositions for Latin-1 Supplement
                chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
                chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
                chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
                chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
                chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
                chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
                chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
                chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
                chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
                chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
                chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
                chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
                chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
                chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
                chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
                chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
                chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
                chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
                chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
                chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
                chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
                chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
                chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
                chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
                chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
                chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
                chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
                chr(195).chr(191) => 'y',
                // Decompositions for Latin Extended-A
                chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
                chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
                chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
                chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
                chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
                chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
                chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
                chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
                chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
                chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
                chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
                chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
                chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
                chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
                chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
                chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
                chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
                chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
                chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
                chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
                chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
                chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
                chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
                chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
                chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
                chr(196).chr(178) => 'IJ', chr(196).chr(179) => 'ij',
                chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
                chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
                chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
                chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
                chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
                chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
                chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
                chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
                chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
                chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
                chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
                chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
                chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
                chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
                chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
                chr(197).chr(146) => 'OE', chr(197).chr(147) => 'oe',
                chr(197).chr(148) => 'R', chr(197).chr(149) => 'r',
                chr(197).chr(150) => 'R', chr(197).chr(151) => 'r',
                chr(197).chr(152) => 'R', chr(197).chr(153) => 'r',
                chr(197).chr(154) => 'S', chr(197).chr(155) => 's',
                chr(197).chr(156) => 'S', chr(197).chr(157) => 's',
                chr(197).chr(158) => 'S', chr(197).chr(159) => 's',
                chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
                chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
                chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
                chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
                chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
                chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
                chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
                chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
                chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
                chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
                chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
                chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
                chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
                chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
                chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
                chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
            ];

            return strtr($string, $chars);
        });
    }
}
