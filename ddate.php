<?php

//
//	Copyright (c) 2009, Jonathan Stroebele <mail@jonathanstroebele.de>
//
//	Permission to use, copy, modify, and/or distribute this software for any
//	purpose with or without fee is hereby granted, provided that the above
//	copyright notice and this permission notice appear in all copies.
//
//	THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
//	WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
//	MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
//	ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
//	WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
//	ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
//	OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
//

	/**
	 *	ddate class
	 *
	 *	@version 0.9
	 *	@author Jonathan Stroebele <mail@jonathanstroebele.de>
	 */
class ddate {

		/**
		 *	Is the output in a HTML context?
		 *	LF or <br /> for a new line
		 *
		 *	@var bool
		 */
	public $html = false;
	
		/**
		 *	Formatstring for the output
		 *
		 *	@var string
		 */
	private $format = 'Today is %{%A, the %e day of %B%} in the YOLD %Y%N%nCelebrate %H';
	
		/**
		 *	Holds the output
		 *
		 *	@var string
		 */
	private $date ='';
	
	private $yold;
	private $season;
	private $day;
	private $yday;
	private $leapYear = false;

	private $gDate = Array();
	
		/**
		 *	Weekdays
		 *
		 *	@var array
		 */
	private $days = Array('Sweetmorn', 'Boomtime', 'Pungenday', 'Prickle-Prickle', 'Setting Orange');
	
		/**
		 *	Weekdays abbreviations
		 *
		 *	@var array
		 */
	private $days_short = Array('SW', 'BT', 'PD', 'PP', 'SO');
	
		/**
		 *	Seasons
		 *
		 *	@var array
		 */
	private $seasons = Array('Chaos', 'Discord', 'Confusion', 'Bureaucracy', 'The Aftermath');
	
		/**
		 *	Seasons abbreviations
		 *
		 *	@var array
		 */
	private $seasons_short = Array('Chs', 'Dsc', 'Cfn', 'Bcy', 'Afm');
	
		/**
		 *	Array of all holydays. Each season has to holydays (on 5th and 50th).
		 *
		 *	@var array
		 */
	private $holydays = Array(
		Array('Mungday', 'Chaoflux'),
		Array('Mojoday', 'Discoflux'),
		Array('Syaday', 'Confuflux'),
		Array('Zaraday', 'Bureflux'),
		Array('Maladay', 'Afflux'),
	);
	
		/**
		 *	Exclamations
		 *
		 *	@var array
		 */
	private $excl = Array(
		'Hail Eris!',
		'All Hail Discordia!',
		'Kallisti!',
		'Fnord.',
		'Or not.',
		'Wibble.',
		'Pzat!',
		'P\'tang!',
		'Frink!',
		/* randomness, from the Net and other places. Feel free to add (after checking with the relevant authorities, of course). */
		'Grudnuk demand sustenance!',
		'Keep the Lasagna flying!',
		'Umlaut Zebra über alles!',
		'',
		'nerdibaer! … wait? what?!',
	);
	
		/**
		 *	Constructor
		 *
		 *	The structur of the array parameter: ('year'=>1, 'month'=>2, 'day'=>3) 
		 *
		 *	@param array $date The date, sea above for the structure
		 */
	public function __construct($date = null) {
		if (empty($date)) {
			$timestamp = time();
			$this->setDate(Array('year'=>date('Y', $timestamp), 'month' => date('m', $timestamp), 'day' => date('d', $timestamp)));
		} else {
			$this->setDate($date);
		}
	}

		/**
		 *	Sets a new format string for the discordian date.
		 *
		 *	@param string $format The format string
		 */
	public function setFormat($format) {
		$this->format = (string) htmlspecialchars($format);
	}
	
		/**
		 *	Alters the gregorian date that is to be converted into the discordian date.
		 *
		 *	The structur of the array parameter: ('year'=>1, 'month'=>2, 'day'=>3) 
		 *
		 *	@param array $date The date, sea above for the structure
		 */
	public function setDate($date) {
		if (!is_array($date)) {
			if ($date[0] == '-') {
				$negative = true;
				$date[0] = 0;
			} else {
				$negative = false;
			}
			$gregor = split('-', $date, 3);
			if (!isset($gregor[0])) { $gregor[0] = 1; }
			if (!isset($gregor[1])) { $gregor[1] = 1; }
			if (!isset($gregor[2])) { $gregor[2] = 1; }
			
			if (!is_numeric($gregor[0])) { $gregor[0] = 1; }
			if (!is_numeric($gregor[1])) { $gregor[1] = 1; }
			if (!is_numeric($gregor[2])) { $gregor[2] = 1; }
			
			$date = Array();
			$date['year'] = ($negative) ? 0 - (int) $gregor[0] : (int) $gregor[0];
			$date['month'] = (int) $gregor[1];
			$date['day'] = (int) $gregor[2];
		}
	
		$this->gDate['year'] =  (int) $date['year'];
		$this->gDate['month'] = (int) $date['month'];
		$this->gDate['day'] = (int) $date['day'];
	
		$cal = Array(0, 31,28,31,30,31,30,31,31,30,31,30,31);
	
		$this->yold = $date['year'] + 1166;
		
		if ($this->isLeapYear((int) $date['year'])) {
			$this->leapYear = true;
		} else {
			$this->leapYear = false;
		}
		
		$this->yday = 0;
		
		$date['month']--;
		while ($date['month'] > 0) {
			$this->yday += $cal[$date['month']];
			$date['month']--;
		}
		$this->yday += --$date['day'];
		
		$this->season = floor($this->yday / 73);
		$this->day = $this->yday  % 73;
	}
	
		/**
		 *	Returns the gregorian date to the current convereted date.
		 *
		 *	@return string
		 */
	public function getDate() {
		return $this->gDate['year'] . '-' . ((strlen($this->gDate['month']) == 1) ? '0'.$this->gDate['month'] : $this->gDate['month']) . '-' . ((strlen($this->gDate['day']) == 1) ? '0'.$this->gDate['day'] : $this->gDate['day']);
	}
	
		/**
		 *	Returns the gregorian date array, and adds before that the 
		 *	timestamp if possible.
		 *
		 *	@return array
		 */
	public function getGArray() {
		$t = mktime(0, 0, 0, $this->gDate['month'], $this->gDate['day'], $this->gDate['year']);
		if ($t) {
			$this->gDate['timestamp'] = $t;
		} else {
			$this->gDate['timestamp'] = 'null';
		}
	
		return $this->gDate;
	}
	
		/**
		 *	This function sets a discordiandate, which is converted to an gregrian
		 *	date, wich is than set.
		 *
		 *	Strucutre of the $eris Array: ('yold'=>3175, 'season'=>5, 'day'=>26)
		 *
		 *	@param array $eris
		 */
	public function setDdate($eris) {
		if (!is_array($eris)) {
			if ($eris[0] == '-') {
				$negative = true;
				$eris[0] = 0;
			} else {
				$negative = false;
			}
			$apple = split('-', $eris, 3);
			if (!isset($apple[0])) { $apple[0] = 1; }
			if (!isset($apple[1])) { $apple[1] = 1; }
			if (!isset($apple[2])) { $apple[2] = 1; }
			
			if (!is_numeric($apple[0])) { $apple[0] = 1; }
			if (!is_numeric($apple[1])) { $apple[1] = 1; }
			if (!is_numeric($apple[2])) { $apple[2] = 1; }
			
			$eris = Array();
			$eris['yold'] = ($negative) ? 0 - (int) $apple[0] : (int) $apple[0];
			$eris['season'] = (int) $apple[1];
			$eris['day'] = (int) $apple[2];
		}
	
	
		$year = $eris['yold'] - 1166;
		$month = 1;
		$yday = --$eris['season'] * 73 + $eris['day'];
		
		$cal = Array(31,28,31,30,31,30,31,31,30,31,30,31);
		
		for($i = 0; $i < 11; $i++) {
			if ($yday > $cal[$i+1]) {
				$yday -= $cal[$i];
				$month++;
			}
		}
		// yday contains now the count of days in the last month
		
		$this->setDate(Array('year'=>$year, 'month'=>$month, 'day'=>$yday));	
	}
	
		/**
		 *	Generates the date for the output. Parses the format string
		 *	in $this->format and writes the output to $this->date.
		 *
		 *	@return string
		 */
	public function getDdate() {
		$this->date = '';
		
		$stStart = false;
		$stEnd = false;
		
		// find St.Tibs day
		for ($i=0; $i < strlen($this->format); $i++) {
			if ($this->format[$i] == '%' && $this->format[$i+1] == '{') {
				$stStart = $i;
			}
			if ($this->format[$i] == '%' && $this->format[$i+1] == '}') {
				$stEnd = $i+1;
			}
		}
	
		for ($i=0; $i < strlen($this->format); $i++) {
			if ($i === $stStart && $this->leapYear === true) {
				// handle St. Tib's Day
				$this->date .= 'St. Tib\'s Day';
				$i = $stEnd;
			} else {
				if ($this->format[$i] == '%') {
					switch ($this->format[$i+1]) {
						case 'A':
							$this->date .= $this->days[$this->yday % 5];
						break;
						
						case 'a':
							$this->date .= $this->days_short[$this->yday % 5];
						break;
						
						case 'B':
							$this->date .= $this->seasons[$this->season];
						break;
						
						case 'b':
							$this->date .= $this->seasons_short[$this->season];
						break;
					
						case 'd':
							$this->date .= $this->day + 1;
						break;
						
						case 'e':
							$this->date .= $this->day+1 . $this->ending($this->day+1);
						break;
						
						case 'H':
							if ($this->day == 4 || $this->day == 49) {
								$this->date .= $this->holydays[$this->season][($this->day == 4) ? 0 : 1];
							}
						break;
					
						case 'N':
							if (!in_array($this->day, Array(4, 49))) {
								$i = strlen($this->format);
							}
						break;
						
						case 'n':
							if ($this->html === true) {
								$this->date .= '<br />';
							} else {
								$this->date .= "\n";
							}
						break;
						
						case 't':
							if ($this->html === true) {
								$this->date .= '&nbsp;&nbsp;&nbsp;&nbsp;';
							} else {
								$this->date .= "\t";
							}
						break;
						
						case 'Y':
							$this->date .= $this->yold;
						break;
						
						case '%':
							$this->date .= '%';
						break;
						
						case '.':
							shuffle($this->excl);
							$this->date .= array_pop($this->excl);
						break;
						
						case 'X':
						break;
					}
					$i++;
				} else {
					$this->date .= $this->format[$i];
				}
			}	
		}
		return $this->date;
	}
	
		/**
		 *	Return an array of all date parts.
		 *
		 *	@return array
		 */
	public function getDArray() {
		return Array('yold'=>$this->yold, 'season'=>$this->season+1, 'day'=>$this->day+1, 'yday'=>$this->yday+1, 'leapyear'=>($this->leapyear) ? 'true' : 'false');
	}

		/**
		 *	Determines the last digit of a number and returns
		 *	`st` for 1, `nd` for 2, `rd` for 3 or `th` for any other
		 *	digit.
		 *
		 *	@param int $day
		 *	@return string
		 */
	private function ending($day) {	
		$day = (string) $day;
		$day = $day[strlen($day) - 1];
	
		switch ((int) $day) {
			case 1:
				return 'st';
			break;
			
			case 2:
				return 'nd';
			break;
			
			case 3:
				return 'rd';
			break;
			
			default:
				return 'th';		
		}
	}
	
		/**
		 *	This function gets a year as a parameter and returns an boolean value,
		 *	which is true for leap years and false for normal years.
		 *
		 *	@param int $year
		 *	@return bool
		 */
	private function isLeapYear($year) {
		if ($year % 4 != 0) {
			return false;
		} else {
			if ($year % 100 != 0) {
				return true;
			} else {
				if ($year % 400 != 0) {
					return false;
				} else {
					return true;
				}
			}
		}
	}
	
	public function __toString() {
		return $this->getDdate();
	}
}
?>