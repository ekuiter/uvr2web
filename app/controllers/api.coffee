App.ApiController = Ember.Controller.extend
  needs: "config"  
  apiPathBinding: "controllers.config.apiPath"
  loggedInBinding: "controllers.config.loggedIn"
  usernameBinding: "controllers.config.username"
  passwordBinding: "controllers.config.password"
  
  url: (call) ->
    "#{@get("apiPath")}call=#{call}&auth=#{@get("sessionId")}"
  
  loginUrl: (->
    "#{@get("apiPath")}auth.login(#{@get("username")},#{@get("password")})"
  ).property("apiPath", "username", "password")

  log: (message) ->
    console.log "API call ##{@get("callId")}: #{message}"
  
  call: (call) ->
    api = @
    @incrementProperty "callId"
    
    errorFunc = (error) ->
      api.log "#{call} (ERROR)"
      api.transitionToRoute "error", error
      error
      
    callFunc = ->
      $.getJSON(api.url(call)).then ((data) ->
        api.log call
        data
      ), errorFunc
      
    loginFunc = ->
      $.getJSON(api.get('loginUrl')).then ((sessionId) ->
        api.log "logged in"
        api.set "sessionId", sessionId
        api.set "loggedIn", true
        callFunc()
      ), errorFunc

    if @get("loggedIn")
      callFunc()
    else
      loginFunc()
      
  logout: ->
    @set "loggedIn", false
    @set "sessionId", null
    @set "username", null
    @set "password", null
    App.get('devices').destruct()

globalController "api"