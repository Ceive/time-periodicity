<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: quickdo
 */

namespace ceive\time\periodicity;

use Ceive\Time\DateTime;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class TickLine
 * @package ceive\time\periodicity
 */
class TickLine implements \Serializable{
	
	/** @var Tick[] */
	public $ticks = [];
	
	/** @var  DateTime */
	protected $last_datetime;
	
	/**
	 * @return Tick
	 */
	public function shift(){
		return array_shift($this->ticks);
	}
	
	/**
	 * @return Tick|null
	 */
	public function first(){
		return $this->ticks?$this->ticks[0]:null;
	}
	
	/**
	 * @return Tick|null
	 */
	public function last(){
		return $this->ticks?$this->ticks[count($this->ticks)-1]:null;
	}
	
	/**
	 * @return bool
	 */
	public function isEmpty(){
		return empty($this->ticks);
	}
	
	/**
	 * @param Tick $tick
	 * @return $this
	 */
	public function append(Tick $tick){
		$this->ticks[] = $tick;
		return $this;
	}
	
	/**
	 * @return \Ceive\Time\DateTime
	 */
	public function getNearestDatetime(){
		if(isset($this->ticks[0])){
			return $this->ticks[0]->getStartDatetime();
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
	
	
	/**
	 * String representation of object
	 * @link http://php.net/manual/en/serializable.serialize.php
	 * @return string the string representation of the object or null
	 * @since 5.1.0
	 */
	public function serialize(){
		return serialize([
			($this->last_datetime instanceof \DateTime?$this->last_datetime->getTimestamp():$this->last_datetime),
			$this->ticks
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
			$this->last_datetime,
			$this->ticks
		) = unserialize($serialized);
	}
	
	/**
	 * @param $lastDatetime
	 */
	public function setLastDatetime($lastDatetime){
		$this->last_datetime = clone $lastDatetime;
	}
}


