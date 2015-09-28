// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

// Place any jQuery/helper plugins in here.





/**
 * WTP jquery custom select tag / droplist css (wolo.pl)
 */
(function($){

    $.fn.dropdownCss = function(options)	{
        //$('.dropstyle select').each(function(index) {
        var defSettings = {
            itemsPerPage: 1,
            itemsPerTransition: 1
        };
        var options = $.extend({}, defSettings, options);
        $(this).each(function(index) {
            var currSelect = $(this);
            var newSel = $('<div class="select">')
                .click(function() { if (!newSel.hasClass('disabled')) newSelOptions.fadeOut().toggle();  })	// hide options list
                .css('position', 'relative');

            $('body').click(function(){newSelOptions.fadeOut().hide();});
            $(newSel).click(function(e){
                e.stopPropagation();
            });

            var newSelinput = $('<input type="hidden">')
                .attr('name', currSelect.attr('name') )
                .addClass(currSelect.attr('class'));
            var newSelOptions = $('<div class="options">');
            var label = $('<div class="label">');


            currSelect.find('option').each(function(index) {
                var currOption = $(this);
                // take label & val from first, replace it later when item is 'selected'
                if (index==0)	{
                    label.html( currOption.html() );
                    newSelinput.val( currOption.val() );
                }
                if (currOption.attr('selected'))	{
                    label.html( currOption.html() );
                    newSelinput.val( currOption.val() );
                }
                var newOption = $('<div class="option">')
                    .html( currOption.html() )
                    .click( function(){
                        newSelinput.val( currOption.val() );			// set hidden input value
                        currSelect.trigger('change', newSelinput);		// call original select onchange
                        label.html( currOption.html() );				// set label
                    })
                    .appendTo(newSelOptions);
            });
            // sklepujemy gotowy markup
            label.appendTo( newSel );
            newSelOptions.appendTo( newSel );
            newSel.appendTo( currSelect.parent() );
            newSelinput.appendTo( currSelect.parent() );
            currSelect.detach();	// not remove! because we use events from it
            newSelOptions.css('top', label.outerHeight());
        });
    }
})(jQuery);





/**
 * WTP jquery custom checkbox / radio css   (wolo.pl)
 */
(function($){

    $.fn.checkradioCss = function(options)	{
        var defSettings = {defaultStyles:false};
        var options = $.extend({}, defSettings, options);
        //var previousName = '';									// name of preceeding input, for radio grouping into one hidden

        $(this).each(function(index) {
            var currInput = $(this);
            var currInputType = $(this).prop('type');
            //if (typeof newInput == 'undefined')
            //	var newInput;
            //	console.log(newInput);
            var newCheck = $('<div class="'+currInputType+'">')
                .addClass('input_'+currInput.prop('name').replace(/[\][]/g, ''))
                .click(function() {
                    // CHECKBOX CLICK
                    if (currInputType=='checkbox')	{

                        if (!newInput.val())	{
                            newCheck.addClass('active');				// set state to active
                            newInput.val( currInput.val() );			// set hidden input value
                        } else	{
                            newCheck.removeClass('active');				// set state to deactive
                            newInput.val('');							// unset hidden input value
                        }
                    }

                    // RADIO CLICK
                    if (currInputType=='radio')	{
                        // uncheck other radios with that name
                        $('.input_'+currInput.prop('name').replace(/[\][]/g, '')).each(function(index) {
                            $(this).removeClass('active');
                        });
                        newCheck.addClass('active');				// set state to active
                        //newInput.val( currInput.val() );			// set hidden input value
                        $('input[type="hidden"][name="'+currInput.prop('name')+'"]').val( currInput.val() );
                    }

                    currInput.trigger('change', newInput);			// call original select onchange
                });

            if (options.defaultStyles)	{
                newCheck.css('border', '1px solid red')
                    .css('width', '25px')
                    .css('height', '25px');
            }

            // make hidden input for every checkbox or every radio group
//			if (currInputType=='checkbox'  ||  (currInputType=='radio' && previousName != currInput.prop('name')))	{
            var newInput = $('<input type="hidden">')
                .attr('name', currInput.attr('name') )
                .addClass(currInput.attr('class'));

            //previousName = currInput.prop('name');
//			}


            // sklepujemy gotowy markup
            newCheck.appendTo( currInput.parent() );
            // if exists hidden with that name, remove it - must be only one
            $('input[type="hidden"][name="'+currInput.attr('name')+'"]').remove();
            newInput.appendTo( currInput.parent() );
            // don't remove old input! we use events from it
            currInput.detach();
        });
    }
})(jQuery);




/**
 * Universal random() function for general use on any selector - get one random element
 */
$.fn.random = function()    {
    var ret = $();

    if(this.length > 0)
        ret = ret.add(this[Math.floor((Math.random() * this.length))]);

    return ret;
};



/**
 * http://lions-mark.com/jquery/scrollTo/
 * @param targetA selector, element, or number.
 * @param options A map of additional options to pass to the method. Supported keys:
 scrollTarget: A element, string, or number which indicates desired scroll position.
 offsetTop: A number that defines additional spacing above scroll target.
 duration: A string or number determining how long the animation will run.
 easing: A string indicating which easing function to use for the transition.
 * @param callback
 * @returns {*}
 */
$.fn.scrollTo = function( target, options, callback ){
    if(typeof options == 'function' && arguments.length == 2){ callback = options; options = target; }
    var settings = $.extend({
        scrollTarget  : target,
        offsetTop     : 50,
        duration      : 500,
        easing        : 'swing'
    }, options);
    return this.each(function(){
        var scrollPane = $(this);
        var scrollTarget = (typeof settings.scrollTarget == "number") ? settings.scrollTarget : $(settings.scrollTarget);
        var scrollY = (typeof scrollTarget == "number") ? scrollTarget : scrollTarget.offset().top + scrollPane.scrollTop() - parseInt(settings.offsetTop);
        scrollPane.animate({scrollTop : scrollY }, parseInt(settings.duration), settings.easing, function(){
            if (typeof callback == 'function') { callback.call(this); }
        });
    });
};






