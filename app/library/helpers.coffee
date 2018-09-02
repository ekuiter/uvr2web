window.capitalize = (string) ->
  string[0].toUpperCase() + string.slice(1)

window.typeFromPath = (path) ->
  split = path.split(".")
  split[split.length - 1]

window.typePlural = (type) ->
  type + "s"

window.isOutput = (device) ->
  device is 0 or device is 1

window.valueFromDevice = (device) ->
  (if isOutput(device) then device else device.value)

window.createDeviceClasses = (klass, newKlass) ->
  for type in ["Sensor", "Output", "Heatmeter", "Speedstep"]
    App.set newKlass.replace(/Type/g, type), App[klass].extend()

window.logPromise = (promise) ->
  promise.then (data) ->
    console.log data

window.t = (key) ->
  Ember.I18n.translations[key]
  
window.globalController = (controllerName) ->
  App.set controllerName, App.__container__.lookup("controller:#{controllerName}")