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
 * Class PeriodDay
 * @package ceive\time\periodicity\PeriodType
 */
class PeriodDay extends PeriodMonth{
	
	/**
	 * @param DateTime $dateTime
	 */
	public function nextPeriod(DateTime $dateTime){
		$dateTime->modify('+1 day');
		$dateTime->setTime(0,0,0);
	}
	
	/**
	 * @param Point $point
	 * @param DateTime $dateTime
	 * @return false|int
	 */
	public function makePointTime(Point $point, DateTime $dateTime){
		return $point->mktime($dateTime->getYearNum(),$dateTime->getMonthNum(),$dateTime->getMonthDayNum());
	}
	
	
}


