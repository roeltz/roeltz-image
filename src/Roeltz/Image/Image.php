<?php

namespace Roeltz\Image;

class Image {
	public $resource;
	public $width;
	public $height;
	public $file;
	
	function __construct($resource) {
		$this->resource = $resource;
		$this->width = imagesx($this->resource);
		$this->height = imagesy($this->resource);		
	}

	static function blank($width, $height, Color $color = null) {
		$resource = imagecreatetruecolor($width, $height);
		imagealphablending($resource, false);
		imagesavealpha($resource, true);
		if (!$color)
			$color = new Color(0, 0, 0, 0);
		imagefill($resource, 0, 0, imagecolorallocatealpha($resource, $color->r, $color->g, $color->b, 127 - floor($color->a / 2)));
		return new Image($resource);
	}
	
	static function fromPath($path) {
		$info = getimagesize($path);
		switch($info["mime"]) {
			case "image/png":
				$resource = imagecreatefrompng($path);
				break;
			case "image/jpeg":
				$resource = imagecreatefromjpeg($path);
				break;
			case "image/gif":
				$resource = imagecreatefromgif($path);
				break;
		}
		imagealphablending($resource, true);
		imagesavealpha($resource, true);
		$image = new Image($resource);
		$image->file = $path;
		return $image;
	}
	
	function getColorAt($x, $y) {
		$color = imagecolorat($this->resource, $x, $y);
		$channels = imagecolorsforindex($this->resource, $color);
		return new Color($channels["red"], $channels["green"], $channels["blue"], round((127 - $channels["alpha"]) * 2));
	}
	
	function setColorAt($x, $y, Color $color) {
		imagesetpixel($this->resource, $x, $y, imagecolorallocatealpha($this->resource, $color->r, $color->g, $color->b, 127 - floor($color->a / 2)));
	}
	
	function getRectangle(Rectangle $rectangle) {
		$image = self::blank($width = $rectangle->x2 - $rectangle->x1, $height = $rectangle->y2 - $rectangle->y1);
		imagecopy($image->resource, $this->resource, 0, 0, $rectangle->x1, $rectangle->y1, $width, $height); 
		return $image;
	}
	
	function getCenterSquare() {
		$side = min(array($this->width, $this->height));
		$image = self::blank($side, $side);
		imagecopy($image->resource, $this->resource, 0, 0, ($this->width - $side) / 2, ($this->height - $side) / 2, $side, $side); 
		return $image;
	}
	
	function copy(Image $image, $x, $y) {
		imagecopy($this->resource, $image->resource, $x, $y, 0, 0, $image->width, $image->height);
	}
	
	function rescale($x) {
		$image = self::blank($width = $this->width * $x, $height = $this->height * $x);
		imagecopyresampled($image->resource, $this->resource, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
		return $image;
	}
	
	function rescaleToWidth($width) {
		$x = $width / $this->width;
		return $this->rescale($x);
	}
	
	function rescaleToMaxWidth($width) {
		if ($width < $this->width)
			return $this->rescaleToWidth($width);
		else
			return $this;
	}

	function rescaleToHeight($height) {
		$x = $height / $this->height;
		return $this->rescale($x);
	}

	function rescaleToMaxHeight($height) {
		if ($height < $this->height)
			return $this->rescaleToHeight($height);
		else
			return $this;
	}

	function rescaleToContain($pixels) {
		$x = $pixels / max($this->width, $this->height);
		return $this->rescale($x);
	}

	function rescaleToCover($pixels) {
		$x = $pixels / min($this->width, $this->height);
		return $this->rescale($x);
	}
	
	function resize($width, $height) {
		$image = self::blank($width, $height);
		imagecopyresampled($image->resource, $this->resource, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
		return $image;
	}
	
	function saveAsPNG($path) {
		imagepng($this->resource, $path);
	}

	function saveAsJPEG($path, $quality = 80) {
		imagejpeg($this->resource, $path, $quality);
	}
}
