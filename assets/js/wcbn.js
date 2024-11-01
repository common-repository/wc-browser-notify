(function($) {
    'use strict';

    var WCBN = {};
    WCBN.actions = {}; // Actions registered in this Obj
    WCBN.poupup_shown = 0;
    WCBN.popup_options = {};

    WCBN.show_popup = function(triggerFunc) {
        
        if(!WCBN.poupup_shown) {
            WCBN.setup_popup_(triggerFunc); // Set Popup options and add HTML markup
            var inst = $('[data-remodal-id="wcbn-modal-'+triggerFunc+'"]').remodal();
            if(typeof WCBN.popup_options.delay != "undefined" && WCBN.popup_options.delay) {
                setTimeout(function(){ inst.open() }, WCBN.popup_options.delay * 1000);
            } else {
                inst.open(); // Open Modal
            }

            // Disable multiple popups, except for scroll
            if(notify_obj.hasOwnProperty("scroll") === false) {   
                WCBN.poupup_shown = 1;
            }
        }
    };

    WCBN.setup_popup_ = function(triggerFunc) {
        if(typeof notify_obj[triggerFunc]["popup_title"] !== "undefined") {
            WCBN.popup_options.title = notify_obj[triggerFunc]["popup_title"];
        }        
        if(typeof notify_obj[triggerFunc]["popup_text"] !== "undefined") {
            WCBN.popup_options.desc = notify_obj[triggerFunc]["popup_text"];
        }
        if(typeof notify_obj[triggerFunc]["delay"] !== "undefined") {
            WCBN.popup_options.delay = notify_obj[triggerFunc]["delay"];
        }
        if(typeof notify_obj[triggerFunc]["override"] !== "undefined") {
            WCBN.popup_options.override = notify_obj[triggerFunc]["override"];
        }


        var markup = '<div class="remodal" data-remodal-id="wcbn-modal-'+triggerFunc+'" role="dialog" aria-labelledby="modal1Title" aria-describedby="modal1Desc">\
          <button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>\
          <div>\
            <h2 id="modal1Title">'+WCBN.popup_options.title+'</h2>\
            <p id="wcbn-modal-desc">'+WCBN.popup_options.desc+'</p>\
          </div>\
          <br>\
        </div>';
        $("body").append(markup);
    };

    WCBN.actions.open = function() {
        WCBN.show_popup("open");
        
        // Disable multiple popups, except for scroll
        if(notify_obj.hasOwnProperty("scroll") === false) {
            WCBN.poupup_shown = 1;
        }
    };
    
    WCBN.actions.scroll = function() {
        $(window).scroll(function () {
            if ($(window).scrollTop() > $('body').height() / 2) {
                WCBN.show_popup("scroll");
                WCBN.poupup_shown = 1;
            } else {
                //WCBN.poupup_shown = 0; // If to show alert on scroll only once
            }
        });
    };
    
    // Check the Trigger obj for triggers
    for (var triggerFunc in notify_obj) {
        // If a Browser Func
        if (notify_obj.hasOwnProperty(triggerFunc) && typeof WCBN.actions[triggerFunc] == "function") {
            WCBN.actions[triggerFunc]();
        }

        //If a WC Non ajax func, popup should be triggered if trigger set
        if(typeof notify_obj[triggerFunc].trigger !== "undefined" && notify_obj[triggerFunc].trigger) {
            WCBN.show_popup(triggerFunc);
            if(WCBN.popup_options.override) {
                WCBN.poupup_shown = 1;
            }
        } 
    }
})(jQuery);
