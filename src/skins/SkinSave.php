<?php

/*
 *  ____   __   __  _   _    ___    ____    ____    ___   _____
 * / ___|  \ \ / / | \ | |  / _ \  |  _ \  / ___|  |_ _| | ____|
 * \___ \   \ V /  |  \| | | | | | | |_) | \___ \   | |  |  _|
 *  ___) |   | |   | |\  | | |_| | |  __/   ___) |  | |  | |___
 * |____/    |_|   |_| \_|  \___/  |_|     |____/  |___| |_____|
 *
 * Ce système permet de sauvegarder et d'obtenir l'apparence et la tête du joueur.
 * En outre, si vous le souhaitez, vous pouvez également obtenir un bloc représentant la tête du joueur.
 * Cela offre plus de personnalisation et d'options pour afficher les skins et les têtes dans le jeu.
 *
 * @author Synopsie
 * @link https://github.com/Synopsie
 * @version 2.2.1
 *
 */

declare(strict_types=1);

namespace skin\skins;

use JsonException;
use pocketmine\entity\Skin;
use pocketmine\Server;
use RuntimeException;
use function getimagesize;
use function imagealphablending;
use function imagecolorallocatealpha;
use function imagecolortransparent;
use function imagecopyresampled;
use function imagecreatefrompng;
use function imagecreatetruecolor;
use function imagedestroy;
use function imagefill;
use function imagepng;
use function imagesavealpha;
use function imagesetpixel;
use function in_array;
use function intdiv;
use function is_null;
use function ord;
use function strlen;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

class SkinSave {
	public static array $acceptedSkinSize = [
		64 * 32 * 4,
		64 * 64 * 4,
		128 * 128 * 4
	];

	public static array $skin_widght_map = [
		64 * 32 * 4   => 64,
		64 * 64 * 4   => 64,
		128 * 128 * 4 => 128
	];

	public static array $skin_height_map = [
		64 * 32 * 4   => 32,
		64 * 64 * 4   => 64,
		128 * 128 * 4 => 128
	];

	public static function validateSize(int $size) : bool {
		return in_array($size, self::$acceptedSkinSize, true);
	}

	public static function skinDataToImage($skinData, $customWidth = null, $customHeight = null) {
		$size = strlen($skinData);
		if (!self::validateSize($size)) {
			return null;
		}
		$width  = $customWidth ?? self::$skin_widght_map[$size];
		$height = $customHeight ?? self::$skin_height_map[$size];
		$image  = imagecreatetruecolor($width, $height);
		if ($image === false) {
			return null;
		}

		imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
		$skinPos = 0;
		for ($y = 0; $y < $height; $y++) {
			for ($x = 0; $x < $width; $x++) {
				$r   = ord($skinData[$skinPos++]);
				$g   = ord($skinData[$skinPos++]);
				$b   = ord($skinData[$skinPos++]);
				$a   = 127 - intdiv(ord($skinData[$skinPos++]), 2);
				$col = imagecolorallocatealpha($image, $r, $g, $b, $a);
				imagesetpixel($image, $x, $y, $col);
			}
		}
		imagesavealpha($image, true);
		return $image;
	}

	public static function resize_image($file, $w, $h, $crop = true, $src_x = 0, $src_y = 0, $src_w = null, $src_h = null) {
		[$width, $height] = getimagesize($file);
		$src              = imagecreatefrompng($file);
		if (is_null($src_w) || is_null($src_h)) {
			$src_w = $width;
			$src_h = $height;
		}
		$dst = imagecreatetruecolor($w, $h);
		imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
		imagealphablending($dst, false);
		imagesavealpha($dst, true);
		imagecopyresampled($dst, $src, 0, 0, $src_x, $src_y, $w, $h, $src_w, $src_h);
		return $dst;
	}

	public static function savePlayerHead($playerName, $skinData, $path) : void {
		$skinImage = self::skinDataToImage($skinData, 64, 64);
		if ($skinImage === null) {
			return;
		}

		$tempFilePath = tempnam(sys_get_temp_dir(), 'skin');
		imagepng($skinImage, $tempFilePath);
		imagedestroy($skinImage);

		$headImage = self::resize_image($tempFilePath, 8, 8, false, 8, 8, 8, 8);

		$headFilePath = $path . $playerName . ".png";
		imagepng($headImage, $headFilePath);
		imagedestroy($headImage);
		unlink($tempFilePath);
	}

	/**
	 * @throws JsonException
	 */
	public static function getSkin(string $name) : Skin {
		$data = Server::getInstance()->getOfflinePlayerData($name);
		if ($data === null) {
			throw new RuntimeException("Player data not found");
		}
		$data = $data->getTag('Skin')->getValue();
		return new Skin(
			$data['Name']->getValue(),
			$data['Data']->getValue(),
			$data['CapeData']?->getValue() ?? "",
			$data['GeometryName']?->getValue() ?? "",
			$data['GeometryData']?->getValue() ?? ""
		);
	}
}
