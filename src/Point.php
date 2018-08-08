<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: quickdo
 */

namespace ceive\time\periodicity;

use Ceive\Time\DateTime;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Point
 * @package ceive\time\periodicity
 */
class Point implements \Serializable{
	
	public $month;
	
	public $month_day;
	
	public $week_day;
	
	public $time;
	
	public $condition;
	
	public $duration = 3600;
	
	/**
	 * @param string $period_type
	 * @return array|null
	 */
	public function getAvailableProperties($period_type = PeriodType::PERIOD_YEAR){
		$available_rules = [
			PeriodType::PERIOD_YEAR => [
				'month',// Jan to Dec OR 0 to 11 OR January to December
				'month_day',
				'week_day', // для умного смещения по Month_day например - выходные
				'time'
			],
			PeriodType::PERIOD_SEASON => [
				'month',// 1 or 2 or 3
				'month_day',
				'week_day',
				'time'
			],
			PeriodType::PERIOD_MONTH => [
				'month_day',
				'week_day',
				'time'
			],
			PeriodType::PERIOD_WEEK => [
				'week_day',
				'time'
			],
			PeriodType::PERIOD_DAY => [
				'week_day',// например для дней которые выходные
				'time'
			]
		];
		if(isset($available_rules[$period_type])){
			return $available_rules[$period_type];
		}
		return null;
	}
	
	/**
	 * @param $period_type
	 * @param array $properties
	 * @return $this
	 */
	public function assign($period_type, array $properties){
		$available = $this->getAvailableProperties($period_type);
		
		$properties = array_intersect_assoc($properties, $available);
		foreach($properties as $k => $v){
			$this->{$k} = $v;
		}
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getDuration(){
		return $this->duration;
	}
	
	/**
	 * @return array
	 */
	public function getTimeChunks(){
		return array_replace([0,0,0],explode(':',$this->time));
	}
	
	/**
	 * @param $year
	 * @param $month
	 * @param $day
	 * @return false|int
	 */
	public function mktime($year, $month=null, $day=null){
		list($h,$m,$s) = array_replace([0,0,0],explode(':',$this->time));
		
		if(!$month) $month  = $this->month;
		if(!$day)   $day    = $this->month_day;
		
		if(!$month) $month  = date('n');
		if(!$day)   $day    = date('j');
		
		
		return mktime($h,$m,$s,$month, $day, $year);
	}
	
	/**
	 * @param DateTime $datetime
	 * @param $condition
	 * @return bool|null
	 */
	public function checkConditionBlock(DateTime $datetime, $condition){
		
		$block = false;
		foreach($condition as $i => $item){
			
			if($i===0 && !is_array($item)){
				$block = false;
				break;
			}
			
			if(strcasecmp($item,'or')===0 || strcasecmp($item,'and')===0){
				$block = true;
				break;
			}
		}
		
		if(!$block && in_array(count($condition),[1,2,3])){
			list($a,$b,$c) = array_replace([null,null,null],$condition);
			$r = $this->checkCond($datetime, $a,$b,$c);
			if(is_bool($r)){
				return $r;
			}
		}
		
		$value = null;
		$nextOr = false;
		foreach($condition as $item){
			if(is_string($item)){
				if(strcasecmp($item,'or')===0){
					$nextOr = true;
				}
				continue;
			}
			if(in_array(count($item),[1,2,3])){
				list($a,$b,$c) = array_replace([null,null,null],$item);
				$r = $this->checkCond($datetime, $a,$b,$c);
				if(is_bool($r)){
					if($value === null){
						$value = $r;
					}else{
						if($nextOr){
							$value = $value || $r;
						}else{
							$value = $value && $r;
						}
					}
				}
			}
			$nextOr = false;
		}
		return $value;
	}
	
	/**
	 * @param DateTime $datetime
	 * @param $left
	 * @param $operator
	 * @param null $right
	 * @return bool
	 */
	public function checkCond(DateTime $datetime, $left, $operator = null, $right = null){
		$negated = strpos($left,'!')===0;
		/**
		 *
		 * TODO abac condition and context resolver
		 *
		 */
		switch($left){
			case 'weekday': $left = $datetime->getWeekdayNumISO(); break;
			case 'monthday': $left = $datetime->getMonthDayNum(); break;
		}
		
		switch($right){
			case 'last-monthday': $right = $datetime->getMonthDays(); break;
			case 'last-weekday': $right = 6; break;
		}
		
		switch($operator){
			case 'in':
				if(!is_array($right)) return null;
				return in_array($left,$right);
				break;
			case 'not-in':
				if(!is_array($right)) return null;
				return !in_array($left,$right);
				break;
			case 'is-odd':
				return $left % 2 == 0;
				break;
			case 'is-even':
				return $left % 2 > 0;
				break;
			case 'is':
				return $left == $right;
				break;
			case 'not-is':
				return $left != $right;
				break;
			case null:
				
				if($negated){
					return !$left;
				}else{
					return !!$left;
				}
				
				break;
		}
		
		
		return null;
	}
	
	/**
	 * В этом методе формируется тик
	 * @see Tick
	 * @param array $path
	 * @param PeriodType $periodType
	 * @param DateTime $lastDatetime
	 * @return Tick|null
	 */
	public function process(array $path, PeriodType $periodType, DateTime $lastDatetime){
		
		$initial_timestamp = $periodType->makePointTime($this, $lastDatetime);
		
		$initial_datetime = new Datetime();
		$initial_datetime->setTimestamp($initial_timestamp);
		
		$start_datetime = $periodType->stabilize($this, $initial_datetime);
		
		if($this->condition && !$this->checkConditionBlock($start_datetime, $this->condition)){
			return null;
		}
		
		$prepare_datetime = clone $start_datetime;
		$prepare_datetime->modifyBack('3 hour', $lastDatetime);
		
		$duration = $this->getDuration();
		
		$finish_timestamp = ($start_datetime->getTimestamp() + $duration);
		
		if($finish_timestamp < $lastDatetime->getTimestamp()){
			return null;
		}
		
		
		
		
		
		$finish_datetime = clone $start_datetime;
		$finish_datetime->setTimestamp($start_datetime->getTimestamp() + $duration);
		
		
		//todo tests
		$lastDatetime->setTimestamp($finish_timestamp);
		
		return new Tick($path,$initial_datetime,$start_datetime, $duration);
	}
	
	/**
	 * @param $weekDay
	 * @param DateTime $initial_datetime
	 * @param null $strategy
	 * @return DateTime
	 * @throws \Exception
	 */
	public static function stabilizeByWeekDay($weekDay, DateTime $initial_datetime, $strategy = null){
		
		$default_strategy = '<';
		
		$initialWeekDay = $initial_datetime->getWeekdayNumISO();
		$rules_aliases = [
			'>'     => [ 1 => '>', 2 => '>', 3 => '>', 4 => '>', 5 => '>', 6 => '>', 7 => '>', ],
			'<'     => [ 1 => '<', 2 => '<', 3 => '<', 4 => '<', 5 => '<', 6 => '<', 7 => '<', ],
			'<<>'   => [ 1 => '<', 2 => '<', 3 => '<', 4 => '>', 5 => '>', 6 => '>', 7 => '>', ],
			'<>>'   => [ 1 => '<', 2 => '<', 3 => '>', 4 => '>', 5 => '>', 6 => '>', 7 => '>', ],
		];
		
		if($strategy){
			if(isset($rules_aliases[$strategy])){
				$strategy = $rules_aliases[$strategy][$initialWeekDay];
			}else{
				$strategy = $default_strategy;
			}
		}else{
			$strategy = $default_strategy;
		}
		
		switch($strategy){
			default:
				throw new \Exception('Error strategy is not valid "'.$strategy.'"');
				break;
			case '>':
				$increment = self::_aheadWeekdayOffsetDays($weekDay, $initialWeekDay);
				break;
			case '<':
				$increment = self::_behindWeekdayOffsetDays($weekDay, $initialWeekDay);
				break;
		}
		if($increment){
			$start_datetime =  clone $initial_datetime;
			$start_datetime->setMonthDay($initial_datetime->getMonthDayNum() + $increment);
		}else{
			$start_datetime = $initial_datetime;
		}
		return $start_datetime;
	}
	
	public static function _aheadWeekdayOffsetDays($weekDay, $initialWeekDay){
		// стратегия перевода вперед
		$increment = 0;
		if($weekDay > $initialWeekDay){
			$increment = $weekDay - $initialWeekDay; // 6 - 3 = 3
		}elseif($weekDay < $initialWeekDay){
			$increment = 7 - $initialWeekDay;
			$increment+= $weekDay;
		}else{
			//ok
		}
		return $increment;
	}
	
	public static function _behindWeekdayOffsetDays($weekDay, $initialWeekDay){
		// стратегия перевода назад
		$increment = 0;// Субота 6 и Вторник 2
		if($weekDay > $initialWeekDay){
			$increment = 7 - $weekDay; // 7 - 6 = 1
			$increment+= $initialWeekDay; // 1 + 2 = 3
			// 12 - 3 = 9
		}elseif($weekDay < $initialWeekDay){// Среда 3 и Суббота 6
			$increment = $initialWeekDay - $weekDay;
		}else{
			//ok
		}
		if($increment){$increment = -$increment;}
		return $increment;
	}
	
	
	/**
	 * String representation of object
	 * @link http://php.net/manual/en/serializable.serialize.php
	 * @return string the string representation of the object or null
	 * @since 5.1.0
	 */
	public function serialize(){
		return serialize([
			$this->month,
			$this->month_day,
			$this->week_day,
			$this->time
		]);
	}
	
	/**
	 * Constructs the object
	 * @link http://php.net/manual/en/serializable.unserialize.php
	 * @param string $serialized <p>
	 * The string representation of the object.
	 * </p>
	 * @return void
	 * @since 5.1.0
	 */
	public function unserialize($serialized){
		list(
			$this->month,
			$this->month_day,
			$this->week_day,
			$this->time
		) = unserialize($serialized);
	}
}


