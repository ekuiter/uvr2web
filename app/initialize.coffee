window.App = Ember.Application.create()

require "library/helpers"
try
  require "env/config.dev"
catch
  require "env/config.prod"
require "config/router"
require "library/require_folders"

requireFolders [
  "library"
  "mixins"
  "routes"
  "models"
  "views"
  "controllers"
  "helpers"
  "templates"
  "components"
]
