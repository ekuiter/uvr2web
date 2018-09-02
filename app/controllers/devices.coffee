fetchType = (type) ->
  (->
    @fetch type
  ).property()

App.HeaderSubbarController = Ember.Controller.extend {}

App.DevicesController = Ember.Controller.extend
  needs: ["api", "headerSubbar"]
  
  # add or overwrite device with 'type' from 'source' to/in 'destination' at 'index'
  # addDevice "sensor", data.sensors, App.sensors, 0 # add the first sensor
  add: (type, source, destination, index) ->
    device = source[index]
    properties =
      no: index + 1
      type: device.type
      mode: device.mode
      currentPower: device.current_power
      kwh: device.kwh
      mwh: device.mwh
      output: device.output
      value: valueFromDevice(device)
    if destination.get(index)
      destination.get(index).setProperties properties
    else
      destination.push App.get(capitalize(type)).create(properties)
  
  # fills the App.sensors, App.outputs, ... arrays with the respective devices
  populate: (devices, type, typesApi, data) ->
    types = typePlural(type)
    collection = data[typesApi]
    App.set types, []  unless App.get(types)
    i = 0
    while i < collection.length
      devices.add type, collection, App.get(types), i
      i++
  
  # fill all device arrays
  # if called like this: devices("sensors") returns a promise that returns App.sensors
  # if called like this: devices()          additionally issues a new AJAX call
  devices: (type) ->
    devices = @
    @set "devicePromise", @get("controllers.api").call("device.read") if not type or not @get("devicePromise")
    @get("devicePromise").then (data) ->
      devices.populate devices, "sensor", "sensors", data
      devices.populate devices, "output", "outputs", data
      devices.populate devices, "heatmeter", "heat_meters", data
      devices.populate devices, "speedstep", "speed_steps", data
      devices.index.fetch()
      unless App.get("favorites.0")
        App.set "favorites", [
          App.sensors[0]
          App.outputs[0]
          App.heatmeters[0]
          App.speedsteps[0]
        ]
      devices.get("controllers.headerSubbar").set "favorites", App.get("favorites")
      (if type then App.get(type) else null)

  fetch: (type) ->
    App.get("devices").devices type

  sensors: fetchType("sensors")
  outputs: fetchType("outputs")
  heatmeters: fetchType("heatmeters")
  speedsteps: fetchType("speedsteps")
  favorites: fetchType("favorites")
  
  index:
    populate: (type, typesApi, data) ->
      types = typePlural(type)
      collection = data[typesApi]
      for key of collection
        for device in collection[key]
          App.get(types).get(device.id - 1).set "alias", device.alias

    heatmeter: (data) ->
      i = 0
      for key of data.heat_meters
        App.get("heatmeters").get(i).set "alias", key
        i++
      
    fetch: ->
      devices = App.get("devices")
      if not devices.get("indexPromise")
        devices.set "indexPromise", devices.get("controllers.api").call("device.index")
        devices.get("indexPromise").then (data) ->
          devices.index.populate "sensor", "sensors", data
          devices.index.populate "output", "outputs", data
          devices.index.heatmeter data
          devices.index.populate "speedstep", "speed_steps", data

  destruct: (type) ->
    App.get("sensors").clear()
    App.get("outputs").clear()
    App.get("heatmeters").clear()
    App.get("speedsteps").clear()
    App.get("favorites").clear()
    @set "devicePromise", null
    @set "indexPromise", null

globalController "devices"