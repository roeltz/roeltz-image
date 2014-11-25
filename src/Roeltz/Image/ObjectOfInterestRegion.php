<?php

namespace Roeltz\Image;

class ObjectOfInterestRegion extends FloodFillRegion {
	
	const MIN_AREA = 200;
	
	private $xray;
	private $borderColor;
	protected $threshold;
	
	function __construct(Image $image, $threshold, $xray = false) {
		$this->image = $image;
		if ($xray) {
			$this->xray = Image::blank($image->width, $image->height + 5);
			$this->xray->copy($image, 0, 0);
		}
		
		$this->threshold = $threshold;
		$this->borderColor = $this->findBorderColor();
		$point = $this->findStartPoint(new Rectangle(0, 0, $image->width - 1, $image->height - 1));

		if ($point) {
			list($x, $y) = $point;
			$this->floodFill($x, $y);
		} else
			throw new Exception("Image has no object of interest");
		
		$id = uniqid();
		
		if ($this->xray) {
			for($x = 0; $x < $this->image->width; $x++)
				for($y = $this->xray->height - 5; $y < $this->xray->height; $y++)
					$this->xray->setColorAt($x, $y, $this->borderColor);
			$this->xray->save("xray/$id.png");
		}
	}
	
	function matchColor(Color $color) {
		return !$this->borderColor->isSimilar($color, $this->threshold);
	}
	
	function findStartPoint(Rectangle $r) {
		list($x, $y) = $r->getCenter();
		
		if ($this->xray) {
			$red = new Color(255, 0, 0, 255);
			for($i = $x - 5; $i < $x + 5; $i++)
				$this->xray->setColorAt($i, $y, $red);
			for($i = $y - 5; $i < $y + 5; $i++)
				$this->xray->setColorAt($x, $i, $red);
		}
		
		$points = array();
		
		if (!$this->image->getColorAt($x, $y)->isSimilar($this->borderColor, $this->threshold))
			$points[] = array($x, $y);
		elseif ($r->getArea() > self::MIN_AREA) {
			$points[] = $this->findStartPoint(new Rectangle($r->x1, $r->y1, $x, $y));
			$points[] = $this->findStartPoint(new Rectangle($x, $r->y1, $r->x2, $y));
			$points[] = $this->findStartPoint(new Rectangle($r->x1, $y, $x, $r->y2));
			$points[] = $this->findStartPoint(new Rectangle($x, $y, $r->x2, $r->y2));
		}
		
		$max = 0;
		$maxp = null;
		foreach($points as $point) {
			if ($point) {
				list($x, $y) = $point;
				$index = $this->image->getColorAt($x, $y)->diff($this->borderColor)->getIndex() * $this->estimateFillArea($x, $y);
				if ($index > $max) {
					$max = $index;
					$maxp = $point;
				}
			}
		}
		return $maxp;
	}
	
	function findBorderColor() {
		$w = $this->image->width;
		$h = $this->image->height;
		$colors = array();

		for($x = 0, $y = $h - 1; $x < $w; $x++) {
			$colors[] = $this->image->getColorAt($x, 0);
			$colors[] = $this->image->getColorAt($x, $y);
		}

		for($y = 0, $x = $w - 1; $y < $h; $y++) {
			$colors[] = $this->image->getColorAt(0, $y);
			$colors[] = $this->image->getColorAt($x, $y);
		}
		
		$count = array();
		foreach($colors as $color) {
			$i = "{$color->r};{$color->g};{$color->b};{$color->a};";
			if (!isset($count[$i]))
				$count[$i] = 0;
			$count[$i]++;
		}

		arsort($count);
		
		$colors = array();
		$maxcount = max($count);
		$weights = array();
		$i = 0;
		$limit = count($count) * 0.2;
		foreach($count as $rgba=>$c) {
			if (++$i > $limit) break;
			list($r, $g, $b, $a) = explode(";", $rgba);
			$colors[] = new Color((int)$r, (int)$g, (int)$b, (int)$a);
			$weights[] = $c / $maxcount;
		}
		
		return Color::average($colors);
	}
}
