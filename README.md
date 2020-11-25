# Google Analytics Popularity
[![<wunderio>](https://circleci.com/gh/wunderio/google_analytics_popularity/tree/8.x-1.x.svg?style=svg)](<https://app.circleci.com/pipelines/github/wunderio/google_analytics_popularity?branch=8.x-1.x>)

## Description
This module uses the Google APIs Client Library for PHP to pull recent top content
from Google Analytics.

Query (using the Analytics Reporting API) is run on cron intervals and results of
top viewed content are stored into a dedicated entity and is accessible via Views.

Count of page views is shown while editing the content page.

## Why this module
This module is alternative or replacement of Radioactivity module that has number
of issues in D8/D9 environments.

Known main issues:

- No multilingual support for energy (Requires patching)
    - Please see and review: https://www.drupal.org/project/radioactivity/issues/2944947
- Radioactivity field not updatable while content is being edited
- ...

## Pre-requisitions

In order to use this module and to pull recent top content from Google Analytics,
following are required:

- Google API service account .json keyfile.
- Google Analytics View ID

Please ask these from the GA team before installing this module.

Furthermore, please ensure that the site has Drupal private file system enabled
as .json keyfile is stored as managed file.

## Installation

- Include this module in repositories of your composer.json file:
```
"repositories": [
    {
        "type": "composer",
        "url": "https://packages.drupal.org/8"
    },
    {
        "type": "vcs",
        "url": "https://github.com/wunderio/google_analytics_popularity.git"
    }
]
```
- Install as normally
```
composer require drupal/google_analytics_popularity:dev-8.x-1.x
```

## Module usage

- Define module settings at admin/config/system/google_analytics_popularity
- Create relevant Views pages and/or blocks. Use a relevant filter and sort criteria
to get desired outcome of most popular content of the site.
