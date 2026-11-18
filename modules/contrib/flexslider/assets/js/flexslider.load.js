/**
 * @file
 * Loads the FlexSlider library.
 */
(function ($) {

  /**
   * Keeps track of initialized flexslider instances.
   */
  const flexsliderInstances = [];

  /**
   * Initialize the flexslider instance.
   *
   * @param {string} sliderId
   *   Id selector of the flexslider object.
   * @param {object} optionset
   *   The optionset to apply to the flexslider object.
   * @param {object} context
   *   The DOM context.
   * @private
   */
  function flexsliderInit(sliderId, optionset, context) {
    const $slider = $(`#${sliderId}`, context);
    const breakpoint = getBreakpoint();

    // Avoid initializing more than once.
    if ($slider.hasClass("flexslider-processed")) {
      return;
    }
    $slider.addClass("flexslider-processed");

    // Remove width/height attributes.
    $slider.find("ul.slides > li > *").removeAttr("width").removeAttr("height");

    // Initialize the slider.
    if (optionset) {
      // If a breakpoint is set and matches, apply it on initialization.
      if (breakpoint) {
        optionset.minItems = breakpoint.minItems;
        optionset.maxItems = breakpoint.maxItems;
      }

      $slider.flexslider(
        $.extend({}, optionset, {
          start(slider) {
            slider.trigger("start", [slider]);
          },
          before(slider) {
            slider.trigger("before", [slider]);
          },
          after(slider) {
            slider.trigger("after", [slider]);
          },
          end(slider) {
            slider.trigger("end", [slider]);
          },
          added(slider) {
            slider.trigger("added", [slider]);
          },
          removed(slider) {
            slider.trigger("removed", [slider]);
          },
          init(slider) {
            flexsliderInstances.push(slider);
            slider.trigger("init", [slider]);
          },
        })
      );
    } else {
      $slider.flexslider();
    }
  }

  Drupal.behaviors.flexslider = {
    attach(context, settings) {
      const sliders = [];
      // Eliminar la declaración innecesaria de 'id'.

      if (
        typeof settings.flexslider !== "undefined" &&
        typeof settings.flexslider.instances !== "undefined"
      ) {
        // Initialize "asNavFor" sliders first.
        Object.keys(settings.flexslider.instances).forEach(sliderInstanceId => {
          const currentSliderId = sliderInstanceId; // Renombrado para evitar el conflicto con 'id'

          if (
            typeof settings.flexslider.optionsets !== "undefined" &&
            settings.flexslider.instances[currentSliderId] in
              settings.flexslider.optionsets
          ) {
            const options =
              settings.flexslider.optionsets[
                settings.flexslider.instances[currentSliderId]
              ];

            if (options.asNavFor !== "") {
              // Initialize "asNavFor" sliders first.
              flexsliderInit(currentSliderId, options, context); // Usamos 'currentSliderId'
            } else {
              sliders[currentSliderId] = options;
            }
          }
        });
      }

      Object.keys(sliders).forEach(sliderInstanceId => {
        const currentSliderId = sliderInstanceId; // Renombrado para evitar el conflicto con 'id'

        flexsliderInit(
          currentSliderId,
          settings.flexslider.optionsets[
            settings.flexslider.instances[currentSliderId]
          ],
          context
        );
      });
    },
  };

  /**
   * Determines which breakpoint matches the current screen size.
   */
  function getBreakpoint() {
    let breakpoint = null;
    const {
      instances: instances,
      optionsets: optionSets
    } = drupalSettings.flexslider;

    Object.keys(instances).forEach((flexsliderId) => {
      const optionSet = optionSets[instances[flexsliderId]];
      if (!optionSet.responsive) {
        return;
      }

      Object
        .values(optionSet.responsive)
        .forEach(candidate => {
          if (window.matchMedia(candidate.mediaQuery).matches) {
            breakpoint = candidate;
          }
        });
    });

    return breakpoint;
  }

  /**
   * Handle amount of items to show based on configured breakpoints.
   */
  function handleBreakPoints() {
    if (flexsliderInstances.length < 1) {
      return;
    }

    const breakpoint = getBreakpoint();
    if (breakpoint) {
      flexsliderInstances.forEach(flexslider => {
        flexslider.vars.minItems = breakpoint.minItems;
        flexslider.vars.maxItems = breakpoint.maxItems;
      });
    }
  }

  // Apply breakpoints when resizing.
  $(window).resize(handleBreakPoints);
})(jQuery);
