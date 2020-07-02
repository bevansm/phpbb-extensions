# PHPBB 3.3.x Extensions

PHPBB 3.3.x extensions.

For instructions on setting up extensions on your site, please see the [PHPBB wiki](https://www.phpbb.com/extensions/installing/).

- [ISOS](isos) adds an "ISO" button to posts, and a dropdown ISO menu at the bottom of all topics listing the users who have posted in the topic and their post counts.
- [pmimport](pmimport) adds user modules to import both send and received PMs from PHPBB CSV/XML exports. Admins have both modules by default.

## Development

Dev enviroment is dockerized. Everything inside of the repo will be copied into the `ext` folder in the deploy. I highly reccomend pulling down [the phpbb extension skeleton](https://github.com/phpbb-extensions/phpbb-ext-skeleton) and putting it in a "phpbb" folder in this repository if you mirror this setup for development; the template files are helpful.
