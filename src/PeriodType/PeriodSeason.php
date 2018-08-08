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
 * Class PeriodSeason
 * @package ceive\time\periodicity\PeriodType
 */
class PeriodSeason extends PeriodYear{
	
	/**
	 * @param DateTime $dateTime
	 */
	public function nextPeriod(DateTime $dateTime){
		
		$monthNum = $dateTime->getMonthNum();
		
		if($monthNum >= 3 && $monthNum <= 5){
			$monthNum = 6;// to summer
		}elseif($monthNum >= 6 && $monthNum <= 8){
			$monthNum = 9;// to autumn
		}elseif($monthNum >= 9 && $monthNum <= 11){
			$monthNum = 12;// to winter
		}elseif($monthNum <= 2 || $monthNum == 12){
			$monthNum = 15;// to spring
		}
		$dateTime->setDate($dateTime->getYearNum(),$monthNum,1);
		$dateTime->setTime(0,0,0);
	}
	
	/**
	 * @param Point $point
	 * @param DateTime $dateTime
	 * @return false|int
	 */
	public function makePointTime(Point $point, DateTime $dateTime){
		$monthNum = $dateTime->getSeasonMonthNum($dateTime->getSeasonName(), $point->month);
		if($dateTime->getMonthNum() > $monthNum){
			$monthNum = 12 + $monthNum;
		}
		return $point->mktime($dateTime->getYearNum(),$monthNum);
	}
	
	
}


