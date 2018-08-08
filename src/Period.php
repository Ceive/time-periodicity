<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: quickdo
 */

namespace ceive\time\periodicity;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Period
 * @package ceive\time\periodicity
 */
class Period implements \Serializable, \ArrayAccess, \Countable{
	
	/** @var Point[]  */
	public $points = [];
	
	/**
	 * @return int
	 */
	public function count(){
		return count($this->points);
	}
	
	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset){
		return isset($this->points[$offset]);
	}
	
	/**
	 * @param mixed $offset
	 * @return Point
	 */
	public function offsetGet($offset){
		return $this->points[$offset];
	}
	
	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value){
		if($offset===null)$this->points[] = $value;
		else $this->points[$offset] = $value;
	}
	
	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset){
		unset($this->points[$offset]);
	}
	
	/**
	 * String representation of object
	 * @link http://php.net/manual/en/serializable.serialize.php
	 * @return string the string representation of the object or null
	 * @since 5.1.0
	 */
	public function serialize(){
		return serialize($this->points);
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
		$this->points = unserialize($serialized);
	}
}


