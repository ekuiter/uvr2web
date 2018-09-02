App.ErrorRoute = Ember.Route.extend
  setupController: (controller, model) ->
    if model and model.hasOwnProperty("responseJSON")
      message = model.responseJSON.error
      if message is "not logged in"
        App.get("api").logout()
        @transitionTo "login", "expired"
      else if message is "login incorrect"
        @transitionTo "login", "incorrect"
    controller.set "model", model