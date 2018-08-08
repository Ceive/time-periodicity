<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: quickdo
 */

namespace ceive\time\periodicity;

class WorkedComposition{
	
	/** @var  Composition */
	public $composition;
	
	/** @var  TickLineAggregation */
	public $aggregation;
	
	
	public function __construct(Composition $composition = null, TickLineAggregation $aggregation = null){
		$this->composition = $composition;
		
		if($aggregation){
			$this->aggregation = $aggregation;
		}else{
			
			$aggregation = new TickLineAggregation();
			$aggregation->last_datetime = time();
			
			$this->aggregation = $aggregation;
		}
	}
	
	
	public function init(){
		$lines = $this->aggregation;
		$lines->cleanOverdue();
		do{
			$tick = $lines->saturateFrom($this->composition)->firstTick();
			if($tick && !$tick->isActive()){
				break;
			}
			$lines->shiftTick();
		}while(true);
	}
	
	public function getNextTick(){
		return $this->aggregation->firstTick();
	}
	
	
}


