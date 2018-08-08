<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: quickdo
 */

namespace ceive\time\periodicity;
use Ceive\Time\DateTime;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class PeriodType
 * @package ceive\time\periodicity
 */
abstract class PeriodType{
	
	const PERIOD_YEAR   = 'year';
	const PERIOD_SEASON = 'season';
	const PERIOD_MONTH  = 'month';
	const PERIOD_WEEK   = 'week';
	const PERIOD_DAY    = 'day';
	
	const SEASON_PART_FIRST  = 'begin';
	const SEASON_PART_SECOND = 'middle';
	const SEASON_PART_THIRD  = 'ending';
	
	const SEASON_PARTS = [
		self::SEASON_PART_FIRST,
		self::SEASON_PART_SECOND,
		self::SEASON_PART_THIRD,
	];
	
	const PERIODS = [
		self::PERIOD_DAY,
		self::PERIOD_WEEK,
		self::PERIOD_MONTH,
		self::PERIOD_SEASON,
		self::PERIOD_YEAR,
	];
	
	
	/** @var array  */
	public static $week            = [ 'Sun',  'Mon',  'Tue',  'Wed',  'Thu',  'Fri',  'Sat', ];
	public static $week_ru         = [ 'Вс',   'Пн',   'Вт',   'Ср',   'Чт',   'Пт',   'Сб', ];
	public static $week_ru_offset  = -1;
	
	
	public $type;
	
	
	
	/**
	 * Прыжек на следующий период во времени.
	 * для разных типов периодов, разная специфика
	 * @param DateTime $dateTime
	 * @return void
	 */
	abstract public function nextPeriod(DateTime $dateTime);
	
	/**
	 * Создание unix timestamp времени, по настройкам Point
	 * @param Point $point
	 * @param DateTime $dateTime
	 * @return int
	 */
	abstract public function makePointTime(Point $point,DateTime $dateTime);
	
	/**
	 * Стабилизация стартового времени: например смещение на ближейший день недели
	 * @see makePointTime - создание времени
	 * @param Point $point
	 * @param DateTime $initial
	 * @return DateTime
	 */
	public function stabilize(Point $point, DateTime $initial){
		return $initial;
	}
	
	
	
	/**
	 * @var array
	 */
	public static $period_types = [];
	
	/**
	 * @param $type
	 * @return mixed
	 */
	public static function get($type){
		if($type instanceof PeriodType){
			return $type;
		}
		$aliases = [
			self::PERIOD_YEAR   => PeriodType\PeriodYear::class,
			self::PERIOD_SEASON => PeriodType\PeriodSeason::class,
			self::PERIOD_MONTH  => PeriodType\PeriodMonth::class,
			self::PERIOD_WEEK   => PeriodType\PeriodWeek::class,
			self::PERIOD_DAY    => PeriodType\PeriodDay::class,
		];
		if(isset($aliases[$type])){
			if(!isset(self::$period_types[$type])){
				$classname = $aliases[$type];
				self::$period_types[$type] = new $classname();
				self::$period_types[$type]->type = $type;
			}
			return self::$period_types[$type];
		}
		return $type;
	}
	
	/**
	 * @return string
	 */
	public function __toString(){
		return $this->type;
	}
	
	
}


