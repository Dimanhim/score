<?php

declare(strict_types=1);

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Generates PWA icons into web/icons: php yii icons
 */
class IconsController extends Controller
{
    private const ACCENT = [0x0E, 0x8A, 0x4F];

    public function actionIndex(string $font = 'C:\\Windows\\Fonts\\segoeuib.ttf'): int
    {
        if (!is_file($font)) {
            $this->stderr("Font not found: {$font}\n");

            return ExitCode::CONFIG;
        }
        $dir = \Yii::getAlias('@app/web/icons');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $this->draw($font, 192, 0.22, "{$dir}/icon-192.png");
        $this->draw($font, 512, 0.22, "{$dir}/icon-512.png");
        $this->draw($font, 180, 0.0, "{$dir}/apple-touch-icon.png");
        $this->draw($font, 512, 0.0, "{$dir}/icon-maskable-512.png", 0.38);
        $this->stdout("Icons written to {$dir}\n");

        return ExitCode::OK;
    }

    /**
     * Rounded green square with a white «С». $radius is a share of the size; $glyph — letter height share.
     */
    private function draw(string $font, int $size, float $radius, string $path, float $glyph = 0.56): void
    {
        $scale = 4;
        $big = $size * $scale;
        $img = imagecreatetruecolor($big, $big);
        imagesavealpha($img, true);
        imagealphablending($img, false);
        imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));
        imagealphablending($img, true);

        $green = imagecolorallocate($img, ...self::ACCENT);
        $r = (int) round($big * $radius);
        if ($r > 0) {
            imagefilledrectangle($img, $r, 0, $big - $r - 1, $big - 1, $green);
            imagefilledrectangle($img, 0, $r, $big - 1, $big - $r - 1, $green);
            foreach ([[$r, $r], [$big - $r - 1, $r], [$r, $big - $r - 1], [$big - $r - 1, $big - $r - 1]] as [$cx, $cy]) {
                imagefilledellipse($img, $cx, $cy, $r * 2, $r * 2, $green);
            }
        } else {
            imagefilledrectangle($img, 0, 0, $big - 1, $big - 1, $green);
        }

        $white = imagecolorallocate($img, 255, 255, 255);
        $letter = 'С';
        $pt = $big * $glyph;
        $box = imagettfbbox($pt, 0, $font, $letter);
        $w = $box[2] - $box[0];
        $h = $box[1] - $box[7];
        $x = (int) (($big - $w) / 2 - $box[0]);
        $y = (int) (($big + $h) / 2 - $box[1]);
        imagettftext($img, $pt, 0, $x, $y, $white, $font, $letter);

        $out = imagecreatetruecolor($size, $size);
        imagesavealpha($out, true);
        imagealphablending($out, false);
        imagecopyresampled($out, $img, 0, 0, 0, 0, $size, $size, $big, $big);
        imagepng($out, $path, 9);
    }
}
