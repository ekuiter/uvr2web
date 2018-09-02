App.Router.map ->
  @route "login", path: "login/:message"
  @route "admin"
  @route "error", path: "error/:message"
  @route "search", path: "s/:query"
  @resource "device", path: "device/:id"
  @resource "sensors", ->
    @route "sensor", path: ":no"
  @resource "outputs", ->
    @route "output", path: ":no"
  @resource "heatmeters", ->
    @route "heatmeter", path: ":no"
  @resource "speedsteps", ->
    @route "speedstep", path: ":no"