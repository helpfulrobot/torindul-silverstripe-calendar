<?php

/**
 * CalendarEvent.php - torindul-calendar
 * 
 * DataObject to store calendar events.
 *
 * @author George Botley <george@torindul.co.uk>
 * @copyright Copyright © 2014, Torindul Business Solutions
 * @package torindul-calendar
 * @subpackage event
 *
*/
class CalendarEvent extends DataObject
{

    /* Database Fields */
        private static $db = array(
            "Title" => "Varchar",
            "Content" => "HTMLText",
            "StartDate" => "Date",
            "EndDate" => "Date",
            "StartTime" => "Time",
            "EndTime" => "Time",
            "Location" => "Varchar",
            'SortOrder' => 'Int',
        );
        
        /* Singular Name */
        public static $singular_name = "Event";
        
        /* Plural Name */
        public static $plural_name = "Events";
        
        /* Has One Relationship */
        private static $has_one = array('Calendar' => 'Calendar');

        /* CMS Fields */
        public function getCMSFields()
        {
            
            //Fetch curret fields and store in Fields
            $fields = parent::getCMSFields();
            
            //Remove Fields
            $fields->removeFieldFromTab("Root.Main", array(
                "SortOrder",
                "CalendarID",
                "Title",
                "Content",
                "StartDate",
                "StartTime",
                "EndDate",
                "EndTime",
                "Location",
            ));
        
            //Event Title
            $Title = new TextField("Title", "Event Title");
            $Title->setRightTitle("Enter the event title. i.e. sports day.");
                
            //Start Date & Time
            $StartDate = new DateField("StartDate", "Start Date");
            $StartDate->setConfig('showcalendar', 1);
            $StartDate->setConfig('dateformat', 'dd/MM/YYYY');
            $StartTime = new TimeField("StartTime", "Start Time (Optional)");
            $StartTime->setConfig('use_strtotime', 1);

            //End Date & Time				
            $EndDate = new DateField("EndDate", "End Date (Optional)");
            $EndDate->setConfig('showcalendar', 1);
            $EndDate->setConfig('dateformat', 'dd/MM/YYYY');
            $EndTime = new TimeField("EndTime", "End Time (Optional)");
            $StartTime->setConfig('use_strtotime', 1);
            
            //Location
            $Location = new AddressTextField("Location", "Event Location", "AIzaSyA-folYpPWGiFcpBZURJpf610nO6FJtqqQ");
            $Location->SetRightTitle("Optional. Begin typing and you will see address suggestions (Beta). Powered by Google.");
            $Location->addExtraClass("text");
            
            //Event Description
            $Description = new HTMLEditorField("Content", "Event Description");
                
            //Group Start and End Date & Time Fields
            $Times = FieldGroup::create(
                $StartDate,
                $StartTime,
                $EndDate,
                $EndTime
            )->setTitle('Timings');
            
            //Add Fields to the CMS
            $fields->addFieldsToTab("Root.Main", array(
                $Title,
                $Times,
                $Location,
                $Description,
            ));
            
            //Return Fields to the CMS
            return $fields;
        }
        
        /* GridField Summary Fields */
        public static $summary_fields = array(
            "Title" => "Event Name",
        );
        
        /* Set the required fields */
        public function getCMSValidator()
        {
            return new RequiredFields(
                array(
                    'Title',
                    'StartDate',
                    'Content',
                )
            );
        }
        
        /* Set the default field values for the creation of a new record */
        public function populateDefaults()
        {
        }
}
