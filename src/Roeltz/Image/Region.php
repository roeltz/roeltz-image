<?php

namespace Rroeltz\Image;

class Region {
	public $pixels = array();
	private $minX = null;
	private $maxX = null;
	private $minY = null;
	private $maxY = null;
	
	function addPixel(Pixel $pixel) {
		$this->pixels["{$pixel->x};{$pixel->y}"] = $pixel;
		if (is_null($this->minX) || $pixel->x < $this->minX) $this->minX = $pixel->x;
		if (is_null($this->maxX) || $pixel->x > $this->maxX) $this->maxX = $pixel->x;
		if (is_null($this->minY) || $pixel->y < $this->minY) $this->minY = $pixel->y;
		if (is_null($this->maxY) || $pixel->y > $this->maxY) $this->maxY = $pixel->y;
		$this->px[$pixel->x][$pixel->y] = $pixel;
		$this->py[$pixel->y][$pixel->x] = $pixel;
	}
	
	function hasPixelAt($x, $y) {
		return isset($this->pixels["$x;$y"]);
	}
	
	function getArea() {
		return count($this->pixels);
	}
	
	function getBoundingRectangle() {
		return new Rectangle($this->minX, $this->minY, $this->maxX, $this->maxY);
	}
	
	function toImage() {
		if ($this->pixels) {
			$rect = $this->getBoundingRectangle();
			$image = Image::blank($rect->x2 - $rect->x1, $rect->y2 - $rect->y1);
			foreach($this->pixels as $pixel)
				$image->setColorAt($pixel->x - $rect->x1, $pixel->y - $rect->y1, $pixel->color);
			return $image;
		} else
			throw new Exception("Empty region cannot be turned into an image");
	}
}
