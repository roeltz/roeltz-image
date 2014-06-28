<?php

namespace Roeltz\Image;

class Pixel {
	public $x;
	public $y;
	public $color;
	
	function __construct($x, $y, Color $color) {
		$this->x = $x;
		$this->y = $y;
		$this->color = $color;
	}
	
	function isNeighbor(Pixel $pixel) {
		return (abs($this->x - $pixel->x) == 1) && (abs($this->y - $pixel->y) == 1);
	}
}
