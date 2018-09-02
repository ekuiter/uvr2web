window.requireFolders = (folders) ->
  for folder in folders
    modules = window.require.list().filter (module) ->
      new RegExp("^#{folder}/").test module
    require module for module in modules