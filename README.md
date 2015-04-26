Basic SilverStripe Calendar by Torindul
====================

## Introduction

When working with our own clients we found that the majority of customers wanted an easy to use calendar within the site tree. Existing modules, whilst very strong, required the creation of pages to add events. Whilst okay on smaller sites it quickly became a mess on larger projects. torindul-silverstripe-calendar therefore makes makes use of custom DataObjects and GridField to achieve the same. Front end templates are given within the Module but are very limited by design to allow designers freedom to overwrite them.

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

