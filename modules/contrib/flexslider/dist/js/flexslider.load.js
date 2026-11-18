(function () {
  'use strict';

  (function ($) {
    var flexsliderInstances = [];
    function flexsliderInit(sliderId, optionset, context) {
      var $slider = $("#".concat(sliderId), context);
      var breakpoint = getBreakpoint();
      if ($slider.hasClass("flexslider-processed")) {
        return;
      }
      $slider.addClass("flexslider-processed");
      $slider.find("ul.slides > li > *").removeAttr("width").removeAttr("height");
      if (optionset) {
        if (breakpoint) {
          optionset.minItems = breakpoint.minItems;
          optionset.maxItems = breakpoint.maxItems;
        }
        $slider.flexslider($.extend({}, optionset, {
          start: function start(slider) {
            slider.trigger("start", [slider]);
          },
          before: function before(slider) {
            slider.trigger("before", [slider]);
          },
          after: function after(slider) {
            slider.trigger("after", [slider]);
          },
          end: function end(slider) {
            slider.trigger("end", [slider]);
          },
          added: function added(slider) {
            slider.trigger("added", [slider]);
          },
          removed: function removed(slider) {
            slider.trigger("removed", [slider]);
          },
          init: function init(slider) {
            flexsliderInstances.push(slider);
            slider.trigger("init", [slider]);
          }
        }));
      } else {
        $slider.flexslider();
      }
    }
    Drupal.behaviors.flexslider = {
      attach: function attach(context, settings) {
        var sliders = [];
        if (typeof settings.flexslider !== "undefined" && typeof settings.flexslider.instances !== "undefined") {
          Object.keys(settings.flexslider.instances).forEach(function (sliderInstanceId) {
            var currentSliderId = sliderInstanceId;
            if (typeof settings.flexslider.optionsets !== "undefined" && settings.flexslider.instances[currentSliderId] in settings.flexslider.optionsets) {
              var options = settings.flexslider.optionsets[settings.flexslider.instances[currentSliderId]];
              if (options.asNavFor !== "") {
                flexsliderInit(currentSliderId, options, context);
              } else {
                sliders[currentSliderId] = options;
              }
            }
          });
        }
        Object.keys(sliders).forEach(function (sliderInstanceId) {
          var currentSliderId = sliderInstanceId;
          flexsliderInit(currentSliderId, settings.flexslider.optionsets[settings.flexslider.instances[currentSliderId]], context);
        });
      }
    };
    function getBreakpoint() {
      var breakpoint = null;
      var _drupalSettings$flexs = drupalSettings.flexslider,
        instances = _drupalSettings$flexs.instances,
        optionSets = _drupalSettings$flexs.optionsets;
      Object.keys(instances).forEach(function (flexsliderId) {
        var optionSet = optionSets[instances[flexsliderId]];
        if (!optionSet.responsive) {
          return;
        }
        Object.values(optionSet.responsive).forEach(function (candidate) {
          if (window.matchMedia(candidate.mediaQuery).matches) {
            breakpoint = candidate;
          }
        });
      });
      return breakpoint;
    }
    function handleBreakPoints() {
      if (flexsliderInstances.length < 1) {
        return;
      }
      var breakpoint = getBreakpoint();
      if (breakpoint) {
        flexsliderInstances.forEach(function (flexslider) {
          flexslider.vars.minItems = breakpoint.minItems;
          flexslider.vars.maxItems = breakpoint.maxItems;
        });
      }
    }
    $(window).resize(handleBreakPoints);
  })(jQuery);

})();
