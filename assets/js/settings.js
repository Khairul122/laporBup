(function($){
  'use strict';
  $(function(){
    var $body = $("body");
    var navbarClasses = "navbar-danger navbar-success navbar-warning navbar-dark navbar-light navbar-primary navbar-info navbar-pink";
    var sidebarClasses = "sidebar-light sidebar-dark";

    // Set dark theme as default and update UI - with multiple attempts
    function setDarkThemeDefault() {
      $body.removeClass(sidebarClasses).addClass("sidebar-dark");
      $(".sidebar-bg-options").removeClass("selected");
      $("#sidebar-dark-theme").addClass("selected");
    }

    // Force dark theme immediately and persistently
    setDarkThemeDefault();

    // Apply again at multiple intervals to ensure it sticks
    setTimeout(setDarkThemeDefault, 50);
    setTimeout(setDarkThemeDefault, 100);
    setTimeout(setDarkThemeDefault, 500);
    setTimeout(setDarkThemeDefault, 1000);

    // Force reapplication every 2 seconds for the first 10 seconds to ensure it persists
    let attempts = 0;
    const persistentForcer = setInterval(() => {
      setDarkThemeDefault();
      attempts++;
      if (attempts >= 5) {
        clearInterval(persistentForcer);
      }
    }, 2000);

    $(".nav-settings").on("click", function(){
      $("#right-sidebar").toggleClass("open");
    });

    $(".settings-close").on("click", function(){
      $("#right-sidebar,#theme-settings").removeClass("open");
    });

    $("#settings-trigger").on("click", function(){
      $("#theme-settings").toggleClass("open");
    });

    $("#sidebar-light-theme").on("click", function(){
      $body.removeClass(sidebarClasses).addClass("sidebar-light");
      $(".sidebar-bg-options").removeClass("selected");
      $(this).addClass("selected");
    });

    $("#sidebar-dark-theme").on("click", function(){
      $body.removeClass(sidebarClasses).addClass("sidebar-dark");
      $(".sidebar-bg-options").removeClass("selected");
      $(this).addClass("selected");
    });

    $(".tiles").on("click", function(){
      var color = $(this).attr("class").split(" ")[1];
      $(".navbar").removeClass(navbarClasses);
      $(".tiles").removeClass("selected");
      $(this).addClass("selected");
      if(color !== "default"){
        $(".navbar").addClass("navbar-" + color);
      }
    });
  });
})(jQuery);
