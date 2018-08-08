<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: quickdo
 */

namespace ceive\time\periodicity;

use Ceive\Time\DateTime;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class TickLineAggregation
 * @package ceive\time\periodicity
 */
class TickLineAggregation implements \Serializable, \Countable{
	
	/** @var TickLine[] */
	public $lines = [];
	
	/** @var  DateTime|int */
	public $last_datetime;
	
	/**
	 * @param TickLine $line
	 * @return $this
	 */
	public function appendLine(TickLine $line){
		$this->lines[] = $line;
		$this->last_datetime = $line->getLastDatetime();
		return $this;
	}
	
	/**
	 * @param null $dateTime
	 * @return $this
	 */
	public function cleanOverdue($dateTime = null){
		if(!$dateTime){
			$dateTime = time();
		}
		$lines = [];
		foreach($this->lines as $line){
			
			while( ($tick = $line->first()) && $tick->isOverdue($dateTime) ){
				$line->shift();
			}
			
			if(!$line->isEmpty()){
				$lines[] = $line;
			}
			
		}
		
		$this->lines = $lines;
		return $this;
	}
	
	/***
	 * @return Tick|null
	 */
	public function shiftTick(){
		if($this->lines){
			$line = $this->lines[0];
			$tick = $line->shift();
			if($line->isEmpty()) array_shift($this->lines);
			return $tick;
		}
		return null;
	}
	
	/**
	 * @param Composition $composition
	 * @param int $linesLimit
	 * @return $this
	 */
	public function saturateFrom(Composition $composition, $linesLimit = null){
		$linesLimit = $linesLimit?:2;
		$c = $this->count();
		if($c < $linesLimit){
			foreach($composition->generate($this->getLastDatetime()) as $i => $ticksLine){
				if($c < $linesLimit){
					$this->appendLine($ticksLine);
					$c++;
				}else{
					break;
				}
			}
		}
		return $this;
	}
	
	/**
	 * @return Tick|null
	 */
	public function firstTick(){
		if($this->lines){
			
			$tick = null;
			while($this->lines){
				
				$line = $this->lines[0];
				$tick = $line->first();
				if(!$tick){
					array_shift($this->lines);
				}else{
					break;
				}
			}
			
			return $tick;
		}
		return null;
	}
	
	/**
	 * @return TickLine|null
	 */
	public function last(){
		if($this->lines){
			return $this->lines[count($this->lines)-1];
		}
		return null;
	}
	
	/**
	 * @return TickLine|null
	 */
	public function first(){
		if($this->lines){
			return $this->lines[0];
		}
		return null;
	}
	
	
	
	/**
	 * @return \Ceive\Time\DateTime|null
	 */
	public function getLastDatetime(){
		if($this->last_datetime !== null){
			if(!$this->last_datetime instanceof DateTime){
				$ts = intval($this->last_datetime);
				$this->last_datetime = new DateTime();
				$this->last_datetime->setTimestamp($ts);
			}
		}
		return $this->last_datetime;
	}
	
	
	
	public function serialize(){
		return serialize([
			($this->last_datetime instanceof \DateTime?$this->last_datetime->getTimestamp():$this->last_datetime),
			$this->lines
		]);
	}
	
	public function unserialize($serialized){
		list(
			$this->last_datetime,
			$this->lines
			) = unserialize($serialized);
	}
	
	/**
	 * Count elements of an object
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * </p>
	 * <p>
	 * The return value is cast to an integer.
	 * @since 5.1.0
	 */
	public function count(){
		return count($this->lines);
	}
	
	
}


