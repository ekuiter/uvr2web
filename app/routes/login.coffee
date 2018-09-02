App.LoginRoute = Ember.Route.extend
  beforeModel: ->
    @transitionTo "index" if @controllerFor("api").get("loggedIn")
    
  model: (params) ->
    { message: t(params.message) }