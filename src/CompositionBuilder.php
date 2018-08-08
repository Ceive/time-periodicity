<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: quickdo
 */

namespace ceive\time\periodicity;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class CompositionBuilder
 * @package ceive\time\periodicity
 */
class CompositionBuilder{
	
	protected $type;
	
	/**
	 * @param array $definition
	 * @return Composition
	 */
	public function build(array $definition){
		
		$this->type = $definition['type'];
		
		$composition = new Composition($this->type);
		
		foreach($definition['periods'] as $periodDefinition){
			$period = $this->_buildPeriod($periodDefinition);
			$composition->setPeriod(null,$period);
		}
		return $composition;
	}
	
	/**
	 * @param array $definition
	 * @return Period
	 */
	protected function _buildPeriod(array $definition){
		if(empty($definition)){
			return null;
		}else{
			$period = new Period();
			foreach($definition as $pointDefinition){
				$pointDefinition = array_replace([
					'month'     => null,
					'month_day' => null,
					'week_day'  => null,
					'time'      => null,
					'duration'  => null,
				],$pointDefinition);
				
				$point = new Point();
				
				$point->month       = $pointDefinition['month'];
				$point->month_day   = $pointDefinition['month_day'];
				$point->week_day    = $pointDefinition['week_day'];
				$point->time        = $pointDefinition['time'];
				
				$period[] = $point;
			}
			return $period;
		}
	}
	
	
}


