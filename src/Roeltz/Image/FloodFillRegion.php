<?php

namespace Roeltz\Image;

abstract class FloodFillRegion extends Region {
	
	public $image;
	
	function __construct(Image $image, $x, $y) {
		$this->image = $image;
		$this->floodFill($x, $y);
	}
	
	function floodFill($x, $y) {
		$queue = array();
		$queue[] = array($x, $y, null);
		while($queue) {
			list($x, $y, $from) = array_shift($queue);
			
			if (!$this->hasPixelAt($x, $y)
				&& $this->matchColor($color = $this->image->getColorAt($x, $y))) {
				$this->addPixel(new Pixel($x, $y, $color));
				if ($from != "r" && $x > 0) $queue[] = array($x - 1, $y, "l");
				if ($from != "l" && $x < $this->image->width - 1) $queue[] = array($x + 1, $y, "r");
				if ($from != "d" && $y > 0) $queue[] = array($x, $y - 1, "t");
				if ($from != "u" && $y < $this->image->height - 1) $queue[] = array($x, $y + 1, "d");
			}
		}
	}
	
	function estimateFillArea($x, $y) {
		$area = 0;
		$queue = array();
		$queue[] = array($x, $y, null);
		$processed = array();
		while($queue) {
			list($x, $y, $from) = array_shift($queue);
			if (!in_array("$x;$y", $processed) && $this->matchColor($color = $this->image->getColorAt($x, $y))) {
				$area++;
				$processed[] = "$x;$y";
				if ($from != "r" && $x > 0) $queue[] = array($x - 1, $y, "l");
				if ($from != "l" && $x < $this->image->width - 1) $queue[] = array($x + 1, $y, "r");
				if ($from != "d" && $y > 0) $queue[] = array($x, $y - 1, "t");
				if ($from != "u" && $y < $this->image->height - 1) $queue[] = array($x, $y + 1, "d");
			}
		}
		
		return $area;	
	}
	
	abstract function matchColor(Color $color);
}