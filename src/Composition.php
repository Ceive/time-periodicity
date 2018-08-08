<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: quickdo
 */

namespace ceive\time\periodicity;

use Ceive\Text\PluralTemplate;
use Ceive\Time\DateTime;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Composition
 * @package ceive\time\periodicity
 */
class Composition implements \Serializable{
	
	/** @var Period[] */
	public $periods = [];
	
	/** @var  PeriodType|string */
	public $period_type;
	
	/**
	 * Composition constructor.
	 * @param $period_type
	 */
	public function __construct($period_type){
		$this->period_type = $period_type;
		$this->_initialize();
	}
	
	/**
	 * @param $periodIndex
	 * @param Period|array|null $period
	 * @return $this
	 */
	public function setPeriod($periodIndex, Period $period = null){
		if($periodIndex === null){
			$this->periods[] = $period;
		}else{
			if(!array_key_exists($periodIndex, $this->periods)){
				$this->periods = array_replace(array_fill(0, $periodIndex+1, null), $this->periods);
			}
			$this->periods[$periodIndex] = $period;
		}
		return $this;
	}
	
	public function getDescription(){
		$periodType = $this->getPeriodTypeKey();
		$periods = $this->getPeriodsCount();
		$points = $this->getTotalPointsCount();
		
		$periodicity = null;
		$at = [];
		//$periodicity = new PeriodicityInfo($points, $periods, $periodType);
		
		
		$plurals = [
			PeriodType::PERIOD_YEAR 	 => ['год','года','лет'],
			PeriodType::PERIOD_SEASON => ['сезон','сезона','сезонов'],
			PeriodType::PERIOD_MONTH  => ['месяц','месяца','месяцев'],
			PeriodType::PERIOD_WEEK 	 => ['неделя','недели','недель'],
			PeriodType::PERIOD_DAY 	 => ['день','дня','дней'],
		];
		
		
		//$morphsSuffix = [['о','ого','ую'], ['ый', 'ая', 'ое', 'ые']];
		$shortly = [
			PeriodType::PERIOD_YEAR 	 => ['Ежегодно','ежегодн'],
			PeriodType::PERIOD_SEASON => ['Ежесезонно','ежесезонн'],
			PeriodType::PERIOD_MONTH  => ['Ежемесячно','ежемесячн'],
			PeriodType::PERIOD_WEEK 	 => ['Еженедельно','еженедельн'],
			PeriodType::PERIOD_DAY 	 => ['Ежедневно','ежедневн'],
		];
		
		switch(true){
			case $periods===1&&$points===1:
				$periodicity = $shortly[$periodType][0];
				break;
			case $periods > 1:
				$periodicity = PluralTemplate::morph($points, 'Раз','* раза','* раз') . " в " .
				               PluralTemplate::morph($periods, $plurals[$periodType][0], $plurals[$periodType][1], $plurals[$periodType][2]);
				break;
		}
		
		switch($periodType){
			
			case PeriodType::PERIOD_YEAR:
				
				foreach($this->periods as $iPeriod => $period){
					$periodNum = $iPeriod + 1;
					foreach($period->points as $iPoint => $point){
						
						$monthKey = DateTime::invertMonthIdentifier($point->month - 1, 'string');
						$monthDay = "{$point->month_day} {$monthKey}";
						if($point->week_day){
							$monthDay.= "(Смещение на {$point->week_day})";
						}
						
						$t = [];
						if($periods>1) $t[] = "Период {$periodNum}";
						
						$t[] = $monthDay;
						$t[] = "в {$point->time}";
						
						$at[] = implode(", ", $t);
					}
				}
				
				break;
			case PeriodType::PERIOD_SEASON:
				
				foreach($this->periods as $iPeriod => $period){
					$periodNum = $iPeriod + 1;
					foreach($period->points as $iPoint => $point){
						
						$monthKey = PeriodType::SEASON_PARTS[$point->month - 1];
						
						$monthDay = "{$point->month_day} числа";
						if($point->week_day){
							$monthDay.= "(Смещение на {$point->week_day})";
						}
						
						$t = [];
						if($periods>1) $t[] = "Период {$periodNum}";
						
						$t[] = $monthKey;
						$t[] = $monthDay;
						$t[] = "в {$point->time}";
						
						$at[] = implode(", ", $t);
					}
				}
				
				break;
			case PeriodType::PERIOD_MONTH:
				
				foreach($this->periods as $iPeriod => $period){
					$periodNum = $iPeriod + 1;
					foreach($period->points as $iPoint => $point){
						
						$monthDay = "{$point->month_day} числа";
						if($point->week_day){
							$monthDay.= "(Смещение на {$point->week_day})";
						}
						
						$t = [];
						if($periods>1) $t[] = "Период {$periodNum}";
						
						$t[] = $monthDay;
						$t[] = "в {$point->time}";
						
						$at[] = implode(", ", $t);
					}
				}
				
				break;
			case PeriodType::PERIOD_WEEK:
				
				foreach($this->periods as $iPeriod => $period){
					$periodNum = $iPeriod + 1;
					foreach($period->points as $iPoint => $point){
						
						$t = [];
						if($periods>1) $t[] = "Период {$periodNum}";
						
						$t[] = "{$point->week_day}";
						$t[] = "в {$point->time}";
						
						$at[] = implode(", ", $t);
					}
				}
				
				break;
			case PeriodType::PERIOD_DAY:
				
				foreach($this->periods as $iPeriod => $period){
					$periodNum = $iPeriod + 1;
					foreach($period->points as $iPoint => $point){
						$t = [];
						if($periods>1) $t[] = "Период {$periodNum}";
						$t[] = "в {$point->time}";
						$at[] = implode(", ", $t);
					}
				}
				
				break;
			
		}
		
		return [$periodicity, $at];
	}
	
	
	protected function _initialize(){
		if($this->period_type){
			$this->period_type = PeriodType::get($this->period_type);
		}
	}
	
	/**
	 * @return int
	 */
	public function getTotalPointsCount(){
		$points_total_count = 0;
		foreach($this->periods as $period){
			$points_total_count+= $period->count();
		}
		return $points_total_count;
	}
	
	/**
	 * @return array
	 * [periodIndex => ticksCount]
	 */
	public function getCountPointsOnPeriods(){
		$point_per_period = [];
		foreach($this->periods as $index => $period){
			$point_per_period[$index] = $period->count();
		}
		return $point_per_period;
	}
	
	/**
	 * @param $periodIndex
	 * @return bool
	 */
	public function hasPeriod($periodIndex){
		return array_key_exists($periodIndex, $this->periods);
	}
	
	/**
	 * @param $periodIndex
	 * @return bool
	 */
	public function emptyPeriod($periodIndex){
		return !isset($this->periods[$periodIndex]) || !$this->periods[$periodIndex]->count();
	}
	
	/**
	 * @param DateTime|null $lastDatetime
	 * @return \Generator
	 */
	public function generate(DateTime $lastDatetime = null){
		if(!$lastDatetime)$lastDatetime = new DateTime();
		else $lastDatetime = clone $lastDatetime;
		
		while(true){
			$ticksLine = new TickLine();
			foreach($this->calculateComposition($lastDatetime) as $i => $tick){
				$ticksLine->append($tick);
			}
			$ticksLine->setLastDatetime($lastDatetime);
			yield $ticksLine;
		}
	}
	
	
	/**
	 * @param DateTime $lastDatetime
	 * @return Tick[]
	 */
	public function calculateComposition(DateTime $lastDatetime){
		$periodType = $this->period_type;
		$ticks = [];
		foreach($this->periods as $periodIndex => $period){
			foreach($period->points as $pointIndex => $point){
				$result = $point->process([$periodIndex, $pointIndex],$periodType, $lastDatetime);
				if($result){
					$ticks[] = $result;
				}
			}
			$this->period_type->nextPeriod($lastDatetime);
		}
		return $ticks;
	}
	
	/**
	 * @param int $periodIndex
	 * @param int $pointIndex
	 * @return bool
	 */
	public function hasPoint($periodIndex = 0, $pointIndex = 0){
		return isset($this->periods[$periodIndex]->points[$pointIndex]);
	}
	
	/**
	 * @return string
	 */
	public function serialize(){
		return serialize([
			$this->period_type->type,
			$this->periods
		]);
	}
	
	/**
	 * @param string $serialized
	 */
	public function unserialize($serialized){
		list(
			$this->period_type,
			$this->periods
		) = unserialize($serialized);
		$this->_initialize();
	}
	
	/**
	 * @return int
	 */
	public function getPeriodsCount(){
		return count($this->periods);
	}
	
	/**
	 * @return string
	 */
	public function getPeriodTypeKey(){
		return $this->period_type instanceof PeriodType?$this->period_type->type:$this->period_type;
	}
}


