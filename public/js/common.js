//nav-active
jQuery(function($) {
    var path = window.location.href; 
    // because the 'href' property of the DOM element is the absolute path
    $('.menu a').each(function() {
      if (this.href === path) {
        $(this).addClass('active-nav-a');
      }
    });
  });


