<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: quickdo
 */

namespace ceive\time\periodicity\PeriodType;


use Ceive\Time\DateTime;
use ceive\time\periodicity\Point;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class PeriodMonth
 * @package ceive\time\periodicity\PeriodType
 */
class PeriodMonth extends PeriodYear{
	
	/**
	 * @param DateTime $dateTime
	 */
	public function nextPeriod(DateTime $dateTime){
		$dateTime->setDate($dateTime->getYearNum(),$dateTime->getMonthNum()+1,1);
		$dateTime->setTime(0,0,0);
	}
	
	/**
	 * @param Point $point
	 * @param DateTime $dateTime
	 * @return false|int
	 */
	public function makePointTime(Point $point, DateTime $dateTime){
		return $point->mktime($dateTime->getYearNum(),$dateTime->getMonthNum());
	}
	
}


