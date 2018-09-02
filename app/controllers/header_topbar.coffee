App.HeaderTopbarController = Ember.Controller.extend
  navigationItems: [
    { route: "index", translation: t("overview") }
    { route: "sensors", translation: t("sensors") }
    { route: "outputs", translation: t("outputs") }
    { route: "heatmeters", translation: t("heatmeters") }
    { route: "speedsteps", translation: t("speedsteps") }
    { route: "admin", translation: t("admin") }
  ]