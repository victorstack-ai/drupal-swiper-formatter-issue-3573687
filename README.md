# Swiper Formatter

## Introduction

Swiper Formatter provides Drupal integration with
[Swiper](https://swiperjs.com/), one of the most modern swiping and sliding
libraries available. Swiper is mobile-first in layout and gestures and offers a
large number of options for designing custom widgets.

This module is not meant to be yet another image slider. It is a swiping UI
widget for almost any kind of content, in both horizontal and vertical
directions: markup/text fields, media fields (e.g., video), content entity
reference fields, Paragraphs, Views content, and more.

Swiper configuration is managed through config entities, making it reusable
across field formatters and Views styles.

For more information, visit the
[project page](https://www.drupal.org/project/swiper_formatter) on Drupal.org
or submit bug reports and feature suggestions in the
[issue queue](https://www.drupal.org/project/issues/swiper_formatter).

## Requirements

- Drupal core 10 or 11.
- The [Token](https://www.drupal.org/project/token) module.
- The [Swiper](https://swiperjs.com/) JavaScript library, loaded via CDN,
  a local library path, or the bundled asset.

## Installation

Install as you would normally install a contributed Drupal module. For further
information, see
[Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules).

```bash
composer require drupal/swiper_formatter
drush en swiper_formatter
```

## Configuration

1. Navigate to **Administration > Configuration > Content authoring >
   Swiper Formatter** (`/admin/config/content/swiper-formatter`) to manage
   Swiper configuration entities.
2. Create or edit a Swiper configuration to set options such as slides per
   view, autoplay, navigation, pagination, loop, keyboard control, zoom,
   and breakpoint-specific overrides.
3. On any entity display (e.g., **Administration > Structure > Content types >
   [Type] > Manage display**), select one of the Swiper field formatters:
   - **Swiper Images** / **Swiper Images Dialog**
   - **Swiper Entity** / **Swiper Entity Dialog**
   - **Swiper Paragraphs** / **Swiper Paragraphs Dialog**
   - **Swiper Text** / **Swiper Text Dialog**
4. In the formatter settings, choose the Swiper configuration entity to apply.
5. For Views, add a **Swiper Formatter** style plugin under the View's format
   settings.

For more detailed usage instructions, see the
[project page](https://www.drupal.org/project/swiper_formatter).

Refer to the [Swiper API documentation](https://swiperjs.com/swiper-api) for
in-depth information on available options.

## Maintainers

- [nk\_](https://www.drupal.org/u/nk_)
