<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: quickdo
 */

namespace ceive\time\periodicity\PeriodType;


use Ceive\Time\DateTime;
use ceive\time\periodicity\PeriodType;
use ceive\time\periodicity\Point;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class PeriodYear
 * @package ceive\time\periodicity\PeriodType
 */
class PeriodYear extends PeriodType{
	
	/**
	 * @param DateTime $dateTime
	 */
	public function nextPeriod(DateTime $dateTime){
		$dateTime->setDate($dateTime->getYearNum()+1,1,1);
		$dateTime->setTime(0,0,0);
	}
	
	/**
	 * @param Point $point
	 * @param DateTime $dateTime
	 * @return false|int
	 */
	public function makePointTime(Point $point, DateTime $dateTime){
		return $point->mktime($dateTime->getYearNum());
	}
	
	/**
	 * @param Point $point
	 * @param DateTime $initial
	 * @return DateTime
	 */
	public function stabilize(Point $point, DateTime $initial){
		if($point->week_day !== null){
			$start_datetime = $point->stabilizeByWeekDay($point->week_day, $initial, null);
		}else{
			$start_datetime = $initial;
		}
		return $start_datetime;
	}
}


