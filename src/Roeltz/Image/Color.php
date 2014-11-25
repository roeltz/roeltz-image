<?php

namespace Roeltz\Image;

class Color {
	public $r;
	public $g;
	public $b;
	public $a;
	
	static function average(array $colors, array $weights = null) {
		if (!$colors)
			return new Color(0,0,0,0);
		
		$r = array();
		$g = array();
		$b = array();
		$a = array();

		if ($weights)
			foreach($colors as $i=>$color) {
				$r[] = $color->r * $weights[$i];
				$g[] = $color->g * $weights[$i];
				$b[] = $color->b * $weights[$i];
				$a[] = $color->a * $weights[$i];
			}
		else
			foreach($colors as $color) {
				$r[] = $color->r;
				$g[] = $color->g;
				$b[] = $color->b;
				$a[] = $color->a;
			}
		
		$c = count($colors);
		$r = array_sum($r) / $c;
		$g = array_sum($g) / $c;
		$b = array_sum($b) / $c;
		$a = array_sum($a) / $c;
		
		return new Color((int)$r, (int)$g, (int)$b, (int)$a);
	}
	
	function __construct($r, $g, $b, $a) {
		$this->r = $r;
		$this->g = $g;
		$this->b = $b;
		$this->a = $a == 254 ? 255 : $a;
	}
	
	function equal(Color $color) {
		return $color->r == $this->r
				&& $color->g == $this->g
				&& $color->b == $this->b
				&& $color->a == $this->a;
	}
	
	function diff(Color $color) {
		return new Color(
			abs($this->r - $color->r),
			abs($this->g - $color->g),
			abs($this->b - $color->b),
			abs($this->a - $color->a)
		);
	}
	
	function getIndex() {
		return ($this->r / 255 + $this->g / 255 + $this->b / 255) / 3;
	}

	function isSimilar(Color $color, $threshold) {
		return
				($color->r >= $this->r - $threshold) && ($color->r <= $this->r + $threshold)
				&& ($color->g >= $this->g - $threshold) && ($color->g <= $this->g + $threshold)
				&& ($color->b >= $this->b - $threshold) && ($color->b <= $this->b + $threshold)
				&& ($color->a >= $this->a - $threshold) && ($color->a <= $this->a + $threshold);
	}
}
