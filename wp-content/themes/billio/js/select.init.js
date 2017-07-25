(function() {
  "use strict";

  var $=jQuery.noConflict();
    $('select').each(function(i,el) {
      new Select({
        el: el,
        alignToHighlighted: 'always'
      });
    });
}).call(this);