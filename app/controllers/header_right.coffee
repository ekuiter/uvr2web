App.HeaderRightController = Ember.Controller.extend
  needs: "api"
  searchTranslation: t("search")
  
  actions:
    search: (query) ->
      @transitionToRoute "search", query

    logout: ->
      @get("controllers.api").logout()
      @transitionToRoute "login", "loggedOut"