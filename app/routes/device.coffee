App.DeviceRoute = App.AuthenticatedRoute.extend
  model: (params) ->
    if params.id
      idObj = App.Id.create(id: params.id)
      @transitionTo idObj.get("route"), idObj.get("no")
    else
      App.get("devices").get(typePlural(typeFromPath(@routeName))).then (data) ->
        data.filterProperty("no", parseInt(params.no))[0]

createDeviceClasses "DeviceRoute", "TypesTypeRoute"
