---
title: "Visitor Tracker"
section: "extensions"
sortOrder: 999
---

## Installation

You can install the extension via composer using the following command:

```bash
composer require igniterlabs/ti-ext-visitortracker -W
```

Run the database migrations to create the required tables:

```bash
php artisan igniter:up
```

## Getting started

From your TastyIgniter Admin, you can manage visitor tracking settings by navigating to the **Manage > Settings > Visitor Tracker Settings** admin page. Here you can configure the following:

- **Tracker Status**: Toggle to enable or disable visitor tracking.
- **Track Robots**: Enable or disable tracking of robots.
- **Exclude routes:** Specify routes to exclude from tracking, such as `api/*`, `admin/*`, etc. Separate multiple routes with a new line.
- **Exclude paths:** Specify paths to exclude from tracking, such as `/login`, `/register`, etc. Separate multiple paths with a new line.
- **Exclude IP Addresses:** Specify IP addresses to exclude from tracking. Separate multiple IP addresses with a comma.
- **Online Timeout:** Set the timeout duration (in minutes) for online visitors. If a visitor is inactive for this duration, they will be considered offline.
- **Keep Logs Duration:** Set how long to keep visitor logs (in days). After this period, logs will be automatically deleted.
- **GeoIP Reader:** Select the GeoIP reader to use for tracking visitor locations. You can choose from:
  - **IPstack**: Uses ipstack.com for GeoIP tracking.
  - **MaxMind GeoLite2**: Uses MaxMind's GeoLite2 database for GeoIP tracking.
  - **None**: Disables GeoIP tracking.
- **IPstack API Key**: If you choose the IPstack reader, enter your IPstack API key here. You can obtain an API key by signing up at [ipstack.com](https://ipstack.com/).
- Click the **Save** button to apply your changes.

There are two pages in the admin user interface for viewing visitor data:

- Navigate to the **Page Visits** admin page to see a list of visitors and their page views.
- Navigate to the **Pages Visits > Pages Views** admin page to see a list of all page views, including the date, time, and visitor details.
