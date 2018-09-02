App.LoginController = Ember.Controller.extend
  needs: "devices"
  
  actions:
    login: ->
      controller = @
      @get("controllers.devices").fetch().then ->
        previousTransition = controller.get("previousTransition")
        if previousTransition
          controller.set "previousTransition", null
          previousTransition.retry()
        else
          controller.transitionToRoute "index"