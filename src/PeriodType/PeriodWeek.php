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
 * Class PeriodWeek
 * @package ceive\time\periodicity\PeriodType
 */
class PeriodWeek extends PeriodType{
	
	/**
	 * @param DateTime $dateTime
	 */
	public function nextPeriod(DateTime $dateTime){
		$wn = $dateTime->getWeekdayNumISO();
		$increment = Point::_aheadWeekdayOffsetDays(1, $wn);
		if(!$increment){
			$increment = 7;
		}
		$dateTime->setMonthDay($dateTime->getMonthDayNum() + $increment);
	}
	
	/**
	 * @param Point $point
	 * @param DateTime $dateTime
	 * @return false|int
	 */
	public function makePointTime(Point $point, DateTime $dateTime){
		$increment = Point::_aheadWeekdayOffsetDays($point->week_day, $dateTime->getWeekdayNumISO());
		return $point->mktime($dateTime->getYearNum(), $dateTime->getMonthNum(), $dateTime->getMonthDayNum() + $increment);
	}
	
	/**
	 * @param Point $point
	 * @param DateTime $initial
	 * @return DateTime
	 */
	public function stabilize(Point $point, DateTime $initial){
		return $initial;
	}
	
}


