App.DevicesRoute = App.AuthenticatedRoute.extend
  model: ->
    App.get("devices").get typeFromPath(@routeName)

  actions:
    edit: ->
      debugger
      @controller.set "isEditing", true

    save: ->
      @controller.set "isEditing", false

createDeviceClasses "DevicesRoute", "TypesRoute"
