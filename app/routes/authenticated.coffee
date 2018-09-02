App.AuthenticatedRoute = Ember.Route.extend
  beforeModel: (transition) ->
    unless @controllerFor("api").get("loggedIn")
      @controllerFor("login").set "previousTransition", transition
      @transitionTo "login", null
      