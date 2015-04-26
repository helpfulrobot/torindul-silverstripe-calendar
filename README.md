Basic SilverStripe Calendar by Torindul
====================

Features
---------------------

A simple calendar page type making use of DataObject and GridField. It offers:

- Responsive Design
- Easy Administration
- Google Places API Event Location Autocomplete (Beta)
- Month, Week and Day Views with a single year view on smaller displays.

Why we created this module
---------------------

### The Problem

When working with our own clients we found that the majority of customers wanted an easy to use calendar facility yet when searching existing modules we found that, whilst they were very strong, they often required the creation of a Calendar container page and many child pages (one per event). Whilst this is okay on smaller sites it became obvious this would became a pain to manage on larger projects.

### The Solution

With the above problem in mind we created a simple calendar application that provided event administration via a single calendar page type with all events to be held in a DataObject and managed with GridField. We did this instead of ModalAdmin as it allows site administrators to create multiple calendars in different locations throughout the site, if required.

Installation
---------------------

### Requirements

- SilverStripe 3.1 (CMS and Framework)


### Option A. Using Composer (Recommended)

> composer require torindul/torindul-silverstripe-calendar

### Option B. Manually 

To install manually ensure you download and extract the repository files to the root of your SilverStripe installation. We aim to not use absolute URLs in our modules but this can't be guaranteed so be certain to name the module directory 'torindul-silverstripe-calendar'.   

### Post-Installation

1. Instruct SilverStripe to build the requires database tables. Access yourdomain.com/dev/build.
2. Flush your cache to enable the template files in this module to function correctly. Access yourdomain.com/?flush=all.

