App.Device = Ember.Object.extend  
  # id of Sensor no. 10 is 's10'
  # id of Speedstep no. 2 is 'ss2'
  id: (->
    @get("typeApi") + @get("no")
  ).property("typeApi", "no")
  
  alias: ((key, value) ->
    @set "customAlias", value if arguments.length is 2
    if @get("customAlias") and not Ember.isEmpty(@get("customAlias").trim())
      @get "customAlias"
    else
      @get "defaultAlias"
  ).property("customAlias", "defaultAlias")
  
  defaultAlias: (->
    App.Id.create(
      id: @get("id")
      type: typeFromPath(@constructor.toString())
    ).get "defaultAlias"
  ).property("id")
  
  read: (start, end) ->
    start = start or ""
    end = end or ""
    call = "device.read(#{@get("id")}"
    if start and end
      call += ",#{start},#{end}"
    else if start
      call += ",#{start}"
    App.get("api").call "#{call})"

App.Sensor = App.Device.extend
  typeApi: "s"
  typeApiPlural: "sensors"

App.Output = App.Device.extend
  typeApi: "o"
  typeApiPlural: "outputs"

App.Heatmeter = App.Device.extend
  typeApi: "hm"
  typeApiPlural: "heat_meters"

App.Speedstep = App.Device.extend
  typeApi: "ss"
  typeApiPlural: "speed_steps"