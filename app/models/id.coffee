App.Id = Ember.Object.extend
  no: (->
    parseInt @get("id").replace(/\D/g, "")
  ).property("id")
  
  defaultAlias: (->
    "#{t(@get("type"))} #{@get("no")}"
  ).property("type", "no")
  
  route: (->
    id = @get("id")
    return "speedsteps.speedstep" if id.indexOf("ss") > -1
    return "sensors.sensor" if id.indexOf("s") > -1
    return "outputs.output" if id.indexOf("o") > -1
    "heatmeters.heatmeter" if id.indexOf("hm") > -1
  ).property("id")