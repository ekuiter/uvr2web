## uvr2web Ember.js app

**Note**: This was an experiment back in 2014, when I wanted to provide a more
modern and interactive frontend for uvr2web. As of now, no further work on this
is planned.

### Installation

#### Preparations

- `npm install` - installs *bower*, *brunch* and the brunch node modules specified in `package.json`
  (this will create the `node_modules` directory and install the *bower* and *brunch* commands)

- `bower install` - installs the components specified in `bower.json`
  (this will create the `bower_components` directory)

#### Adjustments

Make the following changes, otherwise *brunch* will complain:

- open `bower_components/ember-i18n/.bower.json` and change this: 
  `"scripts": { "test": "rake" }` to:
  `"scripts": [{ "test": "rake" }]`

- open `bower_components/cldr/.bower.json` and add this somewhere:
  `"main": ["plurals.js"]`

- open `bower_components/modernizr/.bower.json` and add this somewhere:
  `"main": ["modernizr.js"]`
  
- open `bower_components/foundation/.bower.json` and change this:
  `"main": [ "css/foundation.css", "js/foundation.js" ]` to `"main": [ "js/foundation.js" ]`

(Make sure NOT to change the `bower.json`, but `.bower.json`files.)

Now to set up the API connection
- rename `env/config.dev.coffee.template` to `env/config.dev.coffee`
- point its `apiPath` property to your uvr2web API
- in your API's `init.inc.php` enable the `debug` flag and set the `$ajax_allowed` variable (typically `http://localhost:3333`)
- don't forget to publish your changes

#### Run

`brunch watch --server` or `brunch w -s` to start a server on `localhost:3333` (local file changes are observed)

#### Build

- `brunch build` or `brunch b` builds the app in the `dev_build` directory (including `env/config.dev.coffee` and not optimizing JS and CSS)
- `brunch build --production` or `brunch b --production` builds the app directly in the `../server` directory, ready to commit the changes to Git (including `env/config.prod.coffee` and JS/CSS optimization)

### Updating
- `npm update`
- `bower update` - make sure to repeat the `.bower.json` adjustments above if necessary!
