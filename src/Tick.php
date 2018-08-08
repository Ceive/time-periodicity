<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: quickdo
 */

namespace ceive\time\periodicity;

use Ceive\Time\DateTime;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Tick
 * @package ceive\time\periodicity
 */
class Tick implements \Serializable{
	
	
	const STATUS_ACTIVE     = 'active';
	const STATUS_OVERDUE    = 'overdue';
	const STATUS_WAIT       = 'wait';
	
	
	
	/** @var array [0,0] */
	protected $point_path;
	
	/** @var  DateTime */
	protected $initial_datetime;
	/** @var  DateTime */
	protected $start_datetime;
	
	/** @var  int */
	protected $duration;
	
	/** @var  DateTime|null */
	protected $_finish_datetime;
	
	/**
	 * Tick constructor.
	 * @param array $path
	 * @param $initialDatetime
	 * @param $startDatetime
	 * @param $duration
	 */
	public function __construct(array $path, $initialDatetime, $startDatetime, $duration){
		$this->point_path       = $path;
		$this->initial_datetime = $initialDatetime;
		$this->start_datetime   = $startDatetime;
		$this->duration         = $duration;
	}
	
	/**
	 * @param null $datetime
	 * @return string
	 */
	public function getStatus($datetime = null){
		if($datetime instanceof \DateTime){
			$datetime = $datetime->getTimestamp();
		}elseif(is_string($datetime)){
			$datetime = strtotime($datetime);
		}else{
			$datetime = time();
		}
		$startTimestamp = $this->getStartDatetime()->getTimestamp();
		$finishTimestamp = $startTimestamp + $this->duration;
		
		if($datetime > $finishTimestamp){
			return self::STATUS_OVERDUE;
		}elseif($datetime >= $startTimestamp && $finishTimestamp > $datetime){
			return self::STATUS_ACTIVE;
		}else{
			return self::STATUS_WAIT;
		}
	}
	
	/**
	 * @param null $datetime
	 * @return bool
	 */
	public function isOverdue($datetime = null){
		return $this->getStatus($datetime) === self::STATUS_OVERDUE;
	}
	
	/**
	 * @param null $datetime
	 * @return bool
	 */
	public function isActive($datetime = null){
		return $this->getStatus($datetime) === self::STATUS_ACTIVE;
	}
	
	/**
	 * @param bool $startAtOne
	 * @return mixed
	 */
	public function getPointPath($startAtOne = false){
		return $startAtOne?[ $this->point_path[0] + 1, $this->point_path[1] + 1]:$this->point_path;
	}
	
	/**
	 * @return DateTime
	 */
	public function getFinishDatetime(){
		if(!$this->_finish_datetime){
			$this->_finish_datetime = new Datetime();
			$this->_finish_datetime->setTimestamp($this->getStartDatetime()->getTimestamp() + $this->duration);
		}
		return $this->_finish_datetime;
	}
	
	/**
	 * @return DateTime
	 */
	public function getInitialDatetime(){
		return $this->initial_datetime;
	}
	
	/**
	 * @return DateTime
	 */
	public function getStartDatetime(){
		return $this->start_datetime;
	}
	
	/**
	 * @return mixed
	 */
	public function getDuration(){
		return $this->duration;
	}
	
	/**
	 * String representation of object
	 * @link http://php.net/manual/en/serializable.serialize.php
	 * @return string the string representation of the object or null
	 * @since 5.1.0
	 */
	public function serialize(){
		return serialize([
			$this->point_path,
			(($this->initial_datetime instanceof \DateTime)?$this->initial_datetime->getTimestamp():$this->initial_datetime),
			(($this->start_datetime instanceof \DateTime)?$this->start_datetime->getTimestamp():$this->start_datetime),
			$this->duration,
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
			$this->point_path,
			$this->initial_datetime,
			$this->start_datetime,
			$this->duration,
			) = unserialize($serialized);
		
		$this->initial_datetime = $this->_wakeupDateTime($this->initial_datetime);
		$this->start_datetime = $this->_wakeupDateTime($this->start_datetime);
		
	}
	
	protected function _wakeupDateTime($datetime){
		if(is_int($datetime)){
			$a = new DateTime();
			$a->setTimestamp($datetime);
			$datetime = $a;
		}
		return $datetime;
	}
	
}


