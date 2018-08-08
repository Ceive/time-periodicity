<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: ceive.time-period
 */

namespace ceive\time\periodicity\tests;


use ceive\time\periodicity\CompositionBuilder;
use ceive\time\periodicity\PeriodType;
use ceive\time\periodicity\TickLineAggregation;
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase{
	
	public function testBasic(){
		
		$builder = new CompositionBuilder();
		$composition = $builder->build([
			'type' => PeriodType::PERIOD_YEAR,
			'periods' => [
				[//first period (1)
					[//point (1.1)
						'month'     => 1,
						'month_day' => 10,
						'week_day'  => null,
						'time'      => '16:00',
						'duration'  => 'day-end',
					]
				]
				
			]
		]);
		
		$tickLineAggregator = new TickLineAggregation();
		$tickLineAggregator->saturateFrom($composition, 5);
		$this->assertCount(5, $tickLineAggregator->lines, 'TickLineAggregation have a five lines');
		
		
	}
	
}


