<?php

/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 3/7/2018
 * Time: 1:12 AM
 */
class Pagination {
	public function genArray($current, $last){
		$delta = 2;
		$left = $current - $delta;
		$right = $current + $delta + 1;
		$range = array();
		$rangeWithDots = array();
		$l = -1;

		for ($i = 1; $i <= $last; $i++)
			if ($i == 1 || $i == $last || $i >= $left && $i < $right)
				array_push($range, $i);

		for($i = 0; $i<count($range); $i++) {
			if ($l != -1)
				if ($range[$i] - $l === 2)
					array_push($rangeWithDots, $l + 1);
				else if ($range[$i] - $l !== 1)
					array_push($rangeWithDots, '...');

			array_push($rangeWithDots, $range[$i]);
			$l = $range[$i];
		}

		return $rangeWithDots;
	}

}