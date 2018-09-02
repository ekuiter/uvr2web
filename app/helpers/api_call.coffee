Ember.Handlebars.registerHelper "api-call", (call, args..., context) ->
  i = 0
  while i < args.length
    if args[i] is "bound"
      args.splice i, 1
      args[i] = @get(args[i])
    i++
  App.get("api").url "#{call}(#{args.join(",")})"