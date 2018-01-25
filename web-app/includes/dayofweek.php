<?php

abstract class Includes_DayOfWeek {
	const SUNDAY = 0;
	const MONDAY = 1;
	const TUESDAY = 2;
	const WEDNESDAY = 3;
	const THURSDAY = 4;
	const FRIDAY = 5;
	const SATURDAY = 6;
	
	public static function getTomorrow($dayNum) {
        return ($dayNum + 1) % 7;
    }
	
	public static function getDayString($dayNum) {

		switch($dayNum) {
			case self::MONDAY:
				return 'Monday';
			case self::TUESDAY: 
				return 'Tuesday';
			case self::WEDNESDAY:
				return 'Wednesday';
			case self::THURSDAY:
				return 'Thursday';
			case self::FRIDAY:
				return 'Friday';
			case self::SATURDAY:
				return 'Saturday';
			default:
				return 'Sunday';
		}
	}
	
	public static function getDays() {
		return [
			self::SUNDAY,
			self::MONDAY,
			self::TUESDAY,
			self::WEDNESDAY,
			self::THURSDAY,
			self::FRIDAY,
			self::SATURDAY
		];
	}
}

