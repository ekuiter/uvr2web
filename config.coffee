exports.config =
  paths:
    public: 'dev_build'
    watched: ['app', 'env']
  files:
    javascripts:
      joinTo:
        'assets/beta.js': /^(bower_components|app|env(.*)\.dev)/
      order:
        before: [
          'bower_components/modernizr/modernizr.js',
          'bower_components/jquery/dist/jquery.js',
          'bower_components/foundation/js/foundation.js',
          'bower_components/fastclick/lib/fastclick.js',
          'bower_components/handlebars/handlebars.js',
          'bower_components/ember/ember.js',
          'bower_components/cldr/plurals.js',
          'bower_components/ember-i18n/lib/i18n.js'
        ]
    stylesheets:
      joinTo:
        'assets/beta.css': /^(bower_components|app\/styles)/
      order:
        before: [
          'bower_components/normalize.css/normalize.css',
          'app/styles/foundation.scss'
        ]
    templates:
      precompile: true
      root: 'templates'
      joinTo:
        'assets/beta.js': /^app/
  overrides:
    production:
      paths:
        public: '../php'
      files:
        javascripts:
          joinTo:
            'assets/beta.js': /^(bower_components|app|env(.*)\.prod)/
      optimize: true
      sourceMaps: false
      plugins:
        autoReload:
          enabled: false