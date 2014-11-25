<?php

namespace Roeltz\Image;

class Rectangle {
	public $x1;
	public $y1;
	public $x2;
	public $y2;
	
	function __construct($x1, $y1, $x2, $y2) {
		$this->x1 = $x1;
		$this->y1 = $y1;
		$this->x2 = $x2;
		$this->y2 = $y2;
	}
	
	function getArea() {
		return ($this->x2 - $this->x1) * ($this->y2 - $this->y1);
	}
	
	function getCenter() {
		return array(
			$this->x1 + ($this->x2 - $this->x1) / 2,
			$this->y1 + ($this->y2 - $this->y1) / 2
		);
	}
	
	function limit($x1, $y1, $x2, $y2) {
		if ($this->x1 < $x1) $this->x1 = $x1;
		if ($this->x2 > $x2) $this->x2 = $x2;
		if ($this->y1 < $y1) $this->y1 = $y1;
		if ($this->y2 > $y2) $this->y2 = $y2;
	}
}
