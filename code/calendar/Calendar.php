<?php
	
/**
 * Calendar.php - torindul-calendar
 * 
 * Define a page type called Calendar, an extension of {@link Page}, which will show our events.
 *
 * @author George Botley <george@torindul.co.uk>
 * @copyright Copyright © 2014, Torindul Business Solutions
 * @package torindul-calendar
 * @subpackage calendar
 *
*/
class Calendar extends Page {
	
	/* Singular Name */
	private static $singular_name = "Calendar Page";
	
	/* Plural Name */
	private static $plural_name = "Calendar Pages"; 
	
	/* Page Description */
	private static $description = "A calendar page. Permits the management of events.";
	
	/* Allowed Children */
	private static $allowed_children = "none";
	
	/* Specifiy SiteTree Icon */
	private static $icon = "torindul-calendar/images/calendar.png";
	
	/* Has Many Relationship */
	private static $has_many = array( "Events" => "CalendarEvent" );
	
	/* Can Be Root */
	private static $can_be_root = true;
	
	/* Can Create - If this is false, the class cannot be created in the CMS by regular content authors, only by ADMINs. */
	private static $can_create = true;
	
	//CMS Fields
	public function getCMSFields() {
		
		//Fetch curret fields and store in Fields
		$fields = parent::getCMSFields();
		
		//Add Calendar Events GridField
		$gridFieldConfig = GridFieldConfig_RecordEditor::create(); 
		$gridfield = new GridField("CalendarEvent", "Calendar Events", $this->Events(), $gridFieldConfig);
		$fields->addFieldToTab('Root.CalendarEvents', $gridfield);
		
		//Permit another developer to make modifications to the getCMSFields method using a DataExtension
		$this->extend('updateCalendarCMSFields', $fields);
		
		//Return Fields to the CMS
		return $fields;
		
	}
	
}

class Calendar_Controller extends Page_Controller {
	
	
	public function init() {
		
		/* Inherit initialisation from parent SiteTree */
	    parent::init();
		
		//Include the CSS in the users selected theme to ensure the calendar styles correctly
	    Requirements::css("torindul-silverstripe-calendar/css/calendar.css");
	    
	}
	
	/* Specific the allowed actions on this Controller */
	private static $allowed_actions = array("index", "weekview", "dayview", "view");
	
	/* On opening the calendar page, show the Month View by default. */
	public function index() {
		return $this->renderWith( array("MonthView", "Page") );
	}
	
	/* Open the calendar page in week view */
	public function weekview() {
		return $this->renderWith( array("WeekView", "Page") );
	}
	
	/* Open the calendar page in day view */
	public function dayview() {
		return $this->renderWith( array("DayView", "Page") );
	}
	
	/* Show an individual event */
	public function view() {
		
			/* Get the event from the URL */
			$eventURL = $this->request->param('ID');
			
			/* Strip the URL so we only have the event database ID remaining */
			$explodeEvent = explode("-ID-", $eventURL);
			$EventID = $explodeEvent[1];
			
			/* Retreive the Event from the database */
			$Event = CalendarEvent::get_by_id("CalendarEvent", $EventID);
			
			// If Event does not exist, fail.
			if( ! $Event || ! $Event->exists()) {
				$this->getResponse()->addHeader('Status', '404');
				$this->httpError(404, 'The requested resource could not be found.');
			}
			
			/* Render Event with EventView.ss */
			return $this->customise( 
				array(
					'Title' => $Event->Title . " | " . $this->Title,
					'StartDateLong' => date( "l jS F Y", strtotime( $Event->StartDate ) ),
					'StartDateDay' => date( "jS", strtotime( $Event->StartDate ) ),
					'StartDateMonth' => date( "M", strtotime( $Event->StartDate ) ),
					'EndDateLong' => date( "l jS F Y", strtotime( $Event->EndDate ) ),
					'Event' => $Event,
				)
			)->renderWith( array('EventView', 'Page') );

	}
	
	/**
	* CalendarMonth()
	*
	* @param $month = Month number, excluding trailing zero. i.e. July = 7. Can be passed with $_GET.
	* @param $year = Year number in the format YYYY. Can be passed with $_GET.
	* @author David Walsh (http://davidwalsh.name) - credit for original method code. (2009)
	* @author George Botley <george@torindul.co.uk> - modified for torindul-calendar module, added code comments & updated to PHP 5 (2015).
	*
	* @usage In your template use $CalendarMonth(7, 2015) to output a Calendar view, where this example would show July 2015.
	*/
	function CalendarMonth($month=null, $year=null) {
		
		/* If $month is null check to see if $_GET['month'] exists, if not define it as todays month. */
		if( $month==null ) { $month = ( isset($_GET['month']) ) ? $_GET['month'] : date('m'); }
		
		/* If $year is null check to see if $_GET['year'] exists, if not define it as todays year. */
		if( $year==null ) { $year = ( isset($_GET['year']) ) ? $_GET['year'] : date('Y'); }
		
		/* If $month is less than 1 or greater than 12, default to todays date */
		if( $month <'1' || $month>'12' ) { $month = date('m'); $year = date('Y'); }
	
		/* NOTE: Whilst it is not good practice, and I may change this in a later version, we use tabular output and store this within a php variable */
		/* Form the start of the Calendar in $calendar */
		$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';
	
		/* Create an array to hold the Day Headings for the Calendar */
		$days = array(
			'Sunday',
			'Monday',
			'Tuesday',
			'Wednesday',
			'Thursday',
			'Friday',
			'Saturday',
		);
		
		/* Start the Calendar Row to hold Day Names */
		$calendar.= '<tr class="calendar-row">';
		
		/* Start Table Data to hold day of the week */
		$calendar.= '<td class="calendar-day-head">';
		
		/* Loop through days of the week and display day of the week before closing and opening a new <td> */
		$calendar.= implode('<td class="calendar-day-head">', $days);
		
		/* Once we reach the end of the week day array, close of the last <td> and end the row */
		$calendar.= '</td></tr>';
	
		/* 
		* Lets create some variables to store calendar data
		* 
		* See php.net/date - Returns a string formatted according to the given format string using the given integer timestamp
		* See php.net/mktime - Returns the Unix timestamp corresponding to the arguments given.
		*
		* $running_day = Return the numeric representation for the first day of the month, 0 for Sunday => 6 for Satuday.
		* $days_in_month = The amount of days in the month provided to this method.
		* $days_in_this_week = A counter for the number of days in the current week.
		* $days_counter = A variable that stores the current iterative day and is checked against $days_in_month.
		*/	
		$running_day = date( 'w', mktime( 0, 0, 0, $month, 1, $year ) );
		$days_in_month = date( 't', mktime( 0, 0, 0, $month, 1, $year ) );
		$days_in_this_week = 1;
		$day_counter = 0;
	
		/* Create a row for week number 1 */
		$calendar.= '<tr class="calendar-row">';
	
		/* Start a counter ($x). Whilst the counter is less than $running_day output a blank day as day is from last month */
		for($x = 0; $x < $running_day; $x++) {
			
			/* Output blank day */
			$calendar.= '<td class="calendar-day-np"></td>';
			
			/* Increment the counter for the number of days in this week */
			$days_in_this_week++;
		}
	
		/*
		* After outputting all of the blank days, start a counter ($list_day).
		* Whilst the counter is less than $days_in_month output a day 
		*/
		for($list_day = 1; $list_day <= $days_in_month; $list_day++) {
			
			/* Output opening <td> for the current calendar day */
			$calendar.= '<td class="calendar-day">';
			
				/* Output the opening for the day container */
				$calendar.= '<div class="day-container">';
			
					/* Output the day number */			
					$calendar.= '<div class="day-number">' . $list_day . '</div>';
	
					/* Fetch events for todays date */
					$full_date = $year . "-" . $month . "-" . $list_day;
					$sqlQuery = new sqlQuery();
					$sqlQuery->setFrom('CalendarEvent');
					$sqlQuery->addWhere("('$full_date'>=StartDate) AND ('$full_date'<=EndDate)");
					$sqlQuery->setOrderBy('Title ASC');
					$result = $sqlQuery->execute();
					
					/* Loop through result and output calendar item */
					foreach($result as $row) {
						
						$calendar.= '<p>';
						$calendar.= '<a href="' . $this->URLSegment . '/view/' . $this->stringToSEOURL( $row['Title'] ) . '-ID-' . $row['ID'] . '" ';
						$calendar.= 'title="Open ' . $row["Title"] . ' Event">';
						$calendar.= $row['Title'];
						$calendar.= '</a>';
						$calendar.= '</p>';
						
					}
				
				/* Output the closing tag for the day container */
				$calendar.= '</div>';
			
			/* Output the closing <td> for the current calendar day */	
			$calendar.= '</td>';
			
			/* 
			* Check the counter to see if it equals 6 (the last day in the week).
			* If it does, end the current row.
			*/
			if($running_day == 6) {
				
				/* End the current row */
				$calendar.= '</tr>';
				
				/* If the current day + 1 does not equal the total days in the month then start another row. */
				if( ($day_counter+1) != $days_in_month) {
					$calendar.= '<tr class="calendar-row">';
				}
				
				/* Set the $running_day day to minus 1 (it will be incremented +1 next) */
				$running_day = -1;
				
				/* Reset the $days_in_this_week in this week to 0 (it will be incremented +1 next) */
				$days_in_this_week = 0;
			}
			
			/* Increment $days_in_this_week, $running_day and $day_counter by 1 */
			$days_in_this_week++; $running_day++; $day_counter++;
			
			/* ...AND repeat until we have output all the days in the month */
			
		}
	
		/* 
		* If we have got to this point and still have a few days left in the month
		* then output blank days in their place. 
		*/
		if($days_in_this_week < 8) {
			
			/* Start a counter. Whilst counter is equal to (8 - the total days in this week), continue */
			for($x = 1; $x <= (8 - $days_in_this_week); $x++) {
				
				/* Output opening <td> for the current blank day */
				$calendar.= '<td class="calendar-day-np">';
				
					/* Output day number, as blank, so that a week of blank days has a height in the output */
					$calendar.= '<div class="day-number">&nbsp;</div>';
					
				/* Close <td> for the current blank day */
				$calendar.= '</td>';
				
			}
			
		}
	
		/* Output final row */
		$calendar.= '</tr>';
	
		/* Output the end of the table */
		$calendar.= '</table>';
		
		/* As we havent printed anything on screen yet, return $calendar for use in template with $Calendar */
		return $calendar;
		
	}
	
	/**
	* CalendarMonthText()
	*
	* Return the text for the current viewed month, i.e. February.
	*
	* @author George Botley <george@torindul.co.uk>
	*/
	public function CalendarMonthText() {
		
		/* If no GET variables, return todays month */
		if( !isset($_GET['month']) && !isset($_GET['year']) ) {
			$month = date('F Y');
		}
		
		/* Else, get the month based on GET variables */
		else {
			$month = date('F Y', mktime('0', '0', '0', $_GET['month'], '1' , $_GET['year']) );
		}
		
		return $month;
		
	}
	
	/**
	* EventsInMonth()
	*
	* Return a DataObject for calendar events that fall in the requested month.
	*
	* @param $month = The month to query.
	* @param $year = The year to query in YYYY format.
	* @author George Botley <george@torindul.co.uk>
	*
	* @usage As an example, in tempaltes, use $EventsInMonth(1, 2015) to fetch events in January 2015.
	*/
	public function EventsInMonth($month=null, $year=null) {
		
		/* If month is null define it as todays month */
		if( $month==null ) { $month = date('n'); }
		
		/* If year is null check to see if $_GET['year'] exists, if not define it as todays year. */
		if( $year==null ) { $year = ( isset($_GET['year']) ) ? $_GET['year'] : date('Y'); }
		
		/* Create dates in the format Y-m-d using the requested month and year */
		$month_start = date( 'Y-m-d', mktime('0', '0', '0', $month, '1', $year) );
		$month_end = date( 'Y-m-d', mktime('0', '0', '0', ($month+1), '0', $year) );
				
		/* Fetch the DataObject for events starting or ending in January */
		return DataObject::get(
			"CalendarEvent",
			"(StartDate BETWEEN '$month_start' AND '$month_end') OR (EndDate BETWEEN '$month_start' AND '$month_end')",
			"StartDate ASC"
		);	
					
	}
	
	/**
	* loopMonth()
	*
	* Return a SilverStripe Array for iterating through months in Calendar List View
	*
	* @author George Botley <george@torindul.co.uk>
	* @usage As an example, in templates, use $EventsInMonth(1, 2015) to fetch events in January 2015.
	*/
	public function loopMonth() { 
		
		/* Variable to hold the ArrayList */
		$ArrayList = new ArrayList(); 
		
		/* Month Number Array */
		$months = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
		
		/* Loop and push the number back in to the tempalte */
		foreach($months as $key => $month) {
		
			/* Create ArrayData to push to the ArrayList. The key of the ArrayData item will be the template tag to use within a <% loop %> */
			$resultArray = new ArrayData(array(
				"MonthNumber" => $month,
				"MonthName" => date( 'F', mktime(0, 0, 0, $month, '1', '0') ),
			));
			
			/* Push the $resultArray to the ArrayList */
			$ArrayList->push($resultArray);
		
		}
		
		/* Return the ArrayList to the SilverStripe Template */
		return $ArrayList;
		
	}
	
	/**
	* CalendarWeek()
	*
	* @param $week = Week number, excluding trailing zero. i.e. 53 = last week of the year. Can be passed with $_GET.
	* @param $year = Year number in the format YYYY. Can be passed with $_GET.
	* @author David Walsh (http://davidwalsh.name) - credit for original method code. (2009)
	* @author George Botley <george@torindul.co.uk> - modified for torindul-calendar module, added code comments & updated to PHP 5 (2015).
	*
	* @usage In your template use $CalendarWeek(53, 12, 2015) to output a Calendar view, where this example would show the last week in December of 2015.
	*/
	function CalendarWeek($week=null, $year=null) {
		
		/* If $week is null check to see if $_GET['week'] exists, if not define it as todays week. */
		if( $week==null ) { $week = ( isset($_GET['week']) ) ? $_GET['week'] : date('W'); }
		
		/* If $year is null check to see if $_GET['year'] exists, if not define it as todays year. */
		if( $year==null ) { $year = ( isset($_GET['year']) ) ? $_GET['year'] : date('Y'); }
		
		/* If $week is less than 1 or greater than 53, default to todays date */
		if( $week <'1' || $week>'53' ) { $week = date('W'); $year = date('Y'); }
			
		/* NOTE: Whilst it is not good practice, and I may change this in a later version, we use tabular output and store this within a php variable */
		/* Form the start of the Calendar in $calendar */
		$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';
	
		/* Create an array to hold the Day Headings for the Calendar */
		$days = array(
			'Sunday',
			'Monday',
			'Tuesday',
			'Wednesday',
			'Thursday',
			'Friday',
			'Saturday',
		);
		
		/* Get the first day of the given week to use in $week_start_fulldate mktime(). */
			
			/* Create a new instance of DateTime() within PHP. */
			$week_start_day = new DateTime();
			
			/* Set the ISO date using the $year and $week provided. */
			$week_start_day->setISODate($year, $week);
			
			/* Lets get the day from DateTime() */
			$week_start_day = $week_start_day->format('d');
			
			/* 
			 * As our calendar runs from Sunday to Monday, and DateTime() outputs from Monday, lets minus one.
			 * To do this we will need to convert the String outputted from DateTime() to an integer, at base 10. Then minus one.
			 */
			$week_start_day = intval( $week_start_day, 10 ) - 1;
			
		/* Get the month number from the given week number and year to use in $week_start_fulldate mktime() */
		$week_start_month = date('m', strtotime($year .'-W' . $week) );
		
		/* Get the full date for the first day in this week */
		$week_start_fulldate = date('Y-m-d', mktime('1', '0', '0', $week_start_month, $week_start_day, $year) );
		
		/* Start the Calendar Row to hold Day Names */
		$calendar.= '<tr class="calendar-row">';
		
		/* Start Table Data to hold day of the week */
		$calendar.= '<td class="calendar-day-head">';
		
		/* Loop through days of the week and display day of the week before closing and opening a new <td> */
		$calendar.= implode('<td class="calendar-day-head">', $days);
		
		/* Once we reach the end of the week day array, close off the last <td> and end the row */
		$calendar.= '</td></tr>';
	
		/* 
		* Lets create some variables to store calendar data
		* 
		* See php.net/date - Returns a string formatted according to the given format string using the given integer timestamp
		* See php.net/mktime - Returns the Unix timestamp corresponding to the arguments given.
		*
		* $days_in_week = The amount of days in a week.
		* $days_counter = A variable that stores the current iterative day and is checked against $days_in_month.
		*/	
		$days_in_week = 7;
		$day_counter = 0;
	
		/* Create a row for the week */
		$calendar.= '<tr class="calendar-row">';
	
		/* Start a counter ($list_day). Whilst the counter is less than $days_in_week output a day */
		for($list_day = 1; $list_day <= $days_in_week; $list_day++, $week_start_day++) {
			
			/* Override the $week_start_day at this point to get the day from $week_start_fulldate */
			$week_start_day = date( 'j', strtotime($week_start_fulldate) );
			
			/* Output opening <td> for the current calendar day */
			$calendar.= '<td class="calendar-day">';
			
				/* Output the opening for the day container */
				$calendar.= '<div class="day-container">';
			
					/* Output the day number */			
					$calendar.= '<div class="day-number">' . $week_start_day . '</div>';
	
					/* Fetch events for todays date */
					$full_date = $week_start_fulldate;
					$sqlQuery = new sqlQuery();
					$sqlQuery->setFrom('CalendarEvent');
					$sqlQuery->addWhere("('$full_date'>=StartDate) AND ('$full_date'<=EndDate)");
					$sqlQuery->setOrderBy('Title ASC');
					$result = $sqlQuery->execute();					 
					
					/* Loop through result and output calendar item */
					foreach($result as $row) {
						
						$calendar.= '<p>';
						$calendar.= '<a href="' . $this->URLSegment . '/view/' . $this->stringToSEOURL( $row['Title'] ) . '-ID-' . $row['ID'] . '" ';
						$calendar.= 'title="Open ' . $row["Title"] . ' Event">';
						$calendar.= $row['Title'];
						$calendar.= '</a>';
						$calendar.= '</p>';
						
					}
				
				/* Output the closing tag for the day container */
				$calendar.= '</div>';
			
			/* Output the closing <td> for the current calendar day */	
			$calendar.= '</td>';
			
			/* Increment day by one */
			$week_start_fulldate = date( 'Y-m-d', strtotime($week_start_fulldate . "+1 days") );
			
		}
	
		/* Output final row */
		$calendar.= '</tr>';
	
		/* Output the end of the table */
		$calendar.= '</table>';
		
		/* As we havent printed anything on screen yet, return $calendar for use in template with $Calendar */
		return $calendar;
		
	}
	
	/**
	* CalendarWeekText()
	*
	* Return the text that has the first day of the current week. I.e. 25th January 2015.
	*
	* @author George Botley <george@torindul.co.uk>
	*/
	public function CalendarWeekText() {
		
		/* If no GET variables, return todays week */
		if( !isset($_GET['week']) && !isset($_GET['year']) ) {
			$week = date('W');
			$year = date('Y');
		}
		
		/* Else, get the month based on GET variables */
		else {
			$week = $_GET['week'];
			$year = $_GET['year'];
		}
		
		/* Get the first day of the given week to use in $week_start_fulldate mktime(). */
			
			/* Create a new instance of DateTime() within PHP. */
			$week_start = new DateTime();
			
			/* Set the ISO date using the $year and $week provided. */
			$week_start->setISODate( $year, $week );
			
			/* Lets get the day from DateTime() */
			$week_start = $week_start->format('Y-m-d');
			
			/* Let's return $week_start minus one day as our weeks start on a Sunday */		
			return date('jS F Y', strtotime( $week_start . "-1 days" ) );
		
	}
	
	/**
	* EventsInDay()
	*
	* Return a DataObject for calendar events that fall on the requested day.
	*
	* @param $day = The day to query.
	* @param $month = The month to query.
	* @param $year = The year to query in YYYY format.
	* @author George Botley <george@torindul.co.uk>
	*
	* @usage As an example, in tempaltes, use $EventsInDay(1, 1, 2015) to fetch events on the 1st January 2015.
	*/
	public function EventsInDay($day=null, $month=null, $year=null) {
		
		/* If day is null check to see if $_GET['day'] exists, if not define it as todays day. */
		if( $day==null ) { $day = ( isset($_GET['day']) ) ? $_GET['day'] : date('d'); }
		
		/* If month is null check to see if $_GET['motnh'] exists, if not define it as todays month. */
		if( $month==null ) { $month = ( isset($_GET['month']) ) ? $_GET['month'] : date('m'); }
		
		/* If year is null check to see if $_GET['year'] exists, if not define it as todays year. */
		if( $year==null ) { $year = ( isset($_GET['year']) ) ? $_GET['year'] : date('Y'); }
		
		/* Create the date in the format Y-m-d using the requested day, month and year */
		$day_timestamp = date( 'Y-m-d', mktime('0', '0', '0', $month, $day, $year) );
				
		/* Fetch the DataObject for events starting or ending in January */
		return DataObject::get(
			"CalendarEvent",
			"(StartDate<='$day_timestamp') AND (EndDate>='$day_timestamp')",
			"StartDate ASC"
		);				
	}
	
	/**
	* TodaysDate()
	*
	* Return the full written date for todays date.
	*
	* @author George Botley <george@torindul.co.uk>
	* @usage As an example, in tempaltes, use $TodaysDate to return the long date in UK format.
	*/
	public function TodaysDate() {
		
		if( !isset($_GET['year']) && !isset($_GET['month']) && !isset($_GET['day']) ) {
			return date('l jS F Y');			
		}
		
		else {
			return date( 'l jS F Y', mktime('0', '0', '0', $_GET["month"], $_GET["day"], $_GET["year"]) );
		}
		
	}
	
	/**
	* GETPreviousDay()
	*
	* Return the GET string for the previous day.
	*
	* @author George Botley <george@torindul.co.uk>
	*/
	public function GETPreviousDay() {
		
		/* If there are no GET parameters, lets set the day as yesterday */
		if( !isset($_GET['year']) || !isset($_GET['month']) || !isset($_GET['day']) ) {
			$day = date('d', strtotime('Yesterday'));
			$month = date('m', strtotime('Yesterday'));
			$year = date('Y', strtotime('Yesterday'));
			return "?day=" . $day . "&month=" . $month . "&year=" . $year;
		}
		
		/* If there are GET parameters, ensure we output month and day properly. */
		else {
			
			/* Get the previous day by minusing the current day by 1 */
			$day = date( 'd', mktime('0', '0', '0', $_GET["month"], $_GET["day"]-1, $_GET["year"]) );
			
			/* If the day and month are 1, return the month 12 */
			if( $_GET['day']=='1' && $_GET['month']=='01' ) {
				$month = '12';
			}
			 
			/* Or if the day is 1, return the previous month */
			elseif( $_GET['day']=='1' ) {
				$month = $_GET['month']-1;
			}
			
			/* If none of the above applies, return the current month */
			else {
				$month = $_GET['month'];
			}
			
			/* If the day and month numbers are 1, return the previous year, otherwise return the current year. */
			$year = ( $_GET['day']=='1' && $_GET['month']=='01' ) ? $_GET['year']-1 : $_GET["year"];
			
			return "?day=" . $day . "&month=" . $month . "&year=" . $year;
			
		}
		
	}
	
	/**
	* GETNextDay()
	*
	* Return the GET string for the next day.
	*
	* @author George Botley <george@torindul.co.uk>
	*/
	public function GETNextDay() {
		
		/* If there are no GET parameters, lets set the day as tomorrow */
		if( !isset($_GET['year']) || !isset($_GET['month']) || !isset($_GET['day']) ) {
			$day = date('d', strtotime('Tomorrow'));
			$month = date('m', strtotime('Tomorrow'));
			$year = date('Y', strtotime('Tomorrow'));
			return "?day=" . $day . "&month=" . $month . "&year=" . $year;
		}
		
		/* If there are GET parameters, ensure we output month and day properly. */
		else {
			
			/* Days in current month */
			$days_in_month = cal_days_in_month(CAL_GREGORIAN, $_GET["month"] , $_GET["year"]);
			
			/* Get the previous day by minusing the current day by 1 */
			$day = date( 'd', mktime('0', '0', '0', $_GET["month"], $_GET["day"]+1, $_GET["year"]) );
			
			/* If the current day is equal to the amount of days in this month, return next month. */
			if( $_GET['day']==$days_in_month ) {
				
				/* Increment the current month by 1, but return 1 if the result is 13. */
				$month = ( $_GET['month']+1 == '13' ) ? '01' : $_GET['month']+1;
				
			} 
			
			/* If the above does not apply, return the current month */
			else {
				$month = $_GET['month'];
			}
			
			/* If the day is 31 and the month numbers is 12, return the following year, otherwise return the current year. */
			$year = ( $_GET['day']=='31' && $_GET['month']=='12' ) ? $_GET['year']+1 : $_GET["year"];
			
			return "?day=" . $day . "&month=" . $month . "&year=" . $year;
			
		}
		
	}
	
	/**
	* GETPreviousWeek()
	*
	* Return the GET string for the previous week.
	*
	* @author George Botley <george@torindul.co.uk>
	*/
	public function GETPreviousWeek() {
		
		/* If there are no GET parameters, lets set the week as last week */
		if( !isset($_GET['year']) || !isset($_GET['week']) ) {
			$week = date('W')-1;
			$year = date('Y', strtotime('Last Week'));
			return "?week=" . $week . "&year=" . $year;
		}
		
		/* If there are GET parameters, ensure we output month and day properly. */
		else {
			
			/* Get the previous week by minusing the current week by 1 */
			$week = $_GET['week']-1;
			$year = $_GET['year'];
			
			/* If week is less than or equal to zero, return the previous year, at week 53 */
			if( $week<='0' ) { 
				$week = "53";
				$year = $year-1; 
			}
			
			return "?week=" . $week . "&year=" . $year;
			
		}
		
	}
	
	/**
	* GETNextWeek()
	*
	* Return the GET string for the next week.
	*
	* @author George Botley <george@torindul.co.uk>
	*/
	public function GETNextWeek() {
		
		/* If there are no GET parameters, lets set the week as next week */
		if( !isset($_GET['year']) || !isset($_GET['week']) ) {
			$week = date('W')+1;
			$year = date('Y', strtotime('Next Week'));
			return "?week=" . $week . "&year=" . $year;
		}
		
		/* If there are GET parameters, ensure we output month and day properly. */
		else {
			
			/* Get the next week by increasing the current week by 1 */
			$week = $_GET['week']+1;
			$year = $_GET['year'];
			
			/* If week is more than 53, return the next year, at week 1 */
			if( $week>'53' ) { 
				$week = "1";
				$year = $year+1; 
			}
			
			return "?week=" . $week . "&year=" . $year;
			
		}
		
	}
	
	/**
	* GETPreviousMonth()
	*
	* Return the GET string for the previous month.
	*
	* @author George Botley <george@torindul.co.uk>
	*/
	public function GETPreviousMonth() {
		
		/* If there are no GET parameters, lets set the month as last month */
		if( !isset($_GET['year']) || !isset($_GET['month']) ) {
			$month = date('m', strtotime('Last Month'));
			$year = date('Y', strtotime('Last Month'));
			return "?month=" . $month . "&year=" . $year;
		}
		
		/* If there are GET parameters, ensure we output month and day properly. */
		else {
			
			/* If month is greater than 12, unset the parameter and reset it to 12 */
			if( $_GET['month']>12 ) {
				unset( $_GET['month'] );
				$_GET['month'] = '12';
			}
						
			/* If month is 1 or less than 1 return the 12th month of the previous year */
			if( $_GET['month'] == '1' || $_GET['month']<1 || $_GET['month']>12 ) { 
				$month = '12';
				$year = $_GET['year'] - 1;
			}
			
			/* Otherwise, return the previous month */
			else {
				$month = $_GET['month'] - 1;
				$year = $_GET['year'];
			}
			
			return "?month=" . $month . "&year=" . $year;			
			
		}
		
	}
	
	/**
	* GETNextMonth()
	*
	* Return the GET string for the next month.
	*
	* @author George Botley <george@torindul.co.uk>
	*/
	public function GETNextMonth() {
		
		/* If there are no GET parameters, lets set the month as next month */
		if( !isset($_GET['year']) || !isset($_GET['month']) ) {
			$month = date('m', strtotime('Next Month'));
			$year = date('Y', strtotime('Next Month'));
			return "?month=" . $month . "&year=" . $year;
		}
		
		
		/* If there are GET parameters, ensure we output month and day properly. */
		else {
			
			/* If month is 12, return the 1st month of the previous year */
			if( $_GET['month'] == '12' || $_GET['month']>12 ) {
				$month = '1';
				$year = $_GET['year'] + 1;
			}	
			
			/* Otherwise, return the next month */
			else {
				$month = $_GET['month'] + 1;
				$year = $_GET['year'];
			}
			
			return "?month=" . $month . "&year=" . $year;			
			
		}
		
	}
	
	/**
	* stringToDate()
	*
	* Convert a string to the defined date format
	*
	* @param $string = The string to convert.
	* @param $format = The format (see php.net/date for format)
	* @author George Botley <george@torindul.co.uk>
	*
	* @usage In tempaltes, use $stringToDate('2015-01-01', 'n') to output '1' as the month. (see php.net/date for format)
	*/
	public function stringToDate($string, $format) {
		return date( $format, strtotime($string) );
	}
	
	/**
	* stringToSEOURL()
	*
	* @param $string = The string to convert to an SEO safe URL.
	* @author George Botley <george@torindul.co.uk>
	*
	* @usage Calenadr::stringToSEOURL('Today is Wednesday') outputs as today-is-wednesday
	*/
	public function stringToSEOURL($String) {
		
		/* Strip punctuation */
		$String = preg_replace("/(?![.=$'?%-])\p{P}/u", "", $String);
		
		/* Strip spaces and replace with hyphens */
		$String = str_replace(" ", "-", $String);
		
		/* Convert string to lowercase */
		$String = strtolower($String);
		
		/* Return result */
		return $String;
		
	}
	
}