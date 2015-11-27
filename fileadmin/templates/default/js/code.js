
/* move these to plugins.js */


/**
* wolo.pl jquery custom select tag / droplist css
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
* wolo.pl jquery custom checkbox / radio css
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


/*
* Universal random() function for general use on any selector - get one random element
 */
$.fn.random = function()    {
    var ret = $();

    if(this.length > 0)
        ret = ret.add(this[Math.floor((Math.random() * this.length))]);

    return ret;
};




// WTP main JS - v02
// tested version WTP2: 2.62.4
if (!wtp) var wtp = {

	DEV: false,


	init: function()	{
		if ($('body.dev')[0])  wtp.DEV = true;

		//wtp.checkIfHideAgeSplash();
		//wtp.makeMenuSlidingMarker();	// do it on windowready instead
        //wtp.moveContentsBetweenContainers();
		//wtp.fixFancyboxAnchors();
		//wtp.makeMoreButtonEndings();
		wtp.initWTPinfobox();
		//wtp.cookieMessageShow();
		//$('select').dropdownCss({x:1});
	},


	/**
     * WTP INFOBOX v4.3
     * - on update check below widths for rwdinfo!
     */
    initWTPinfobox: function()  {

		var WTP_infobox = $("#wtp_infobox");
		
		
        WTP_infobox.draggable();

        // restore posision
        var position = [600, 20];
        var positionCookie = $.cookie("wtp-infobox-position");
        if (positionCookie)     position = positionCookie.split(',');   // avoid js error "split() undefined)...

        // unhide infobox
        WTP_infobox.css({
            visibility: 'visible'
        });

        if (position[0] && position[1]) {
            WTP_infobox.css({
                //position: 'fixed',
                visibility: 'visible',
                left: position[0] + 'px',
                top: position[1] + 'px'
            });
            //console.info('wtp infobox position: ' + position[0] + ', ' + position[1]);
        }

        // save position after click release (x,y - left,top)
        WTP_infobox.mouseup(function(){
            $.cookie("wtp-infobox-position",
                $(this).position().left + ',' + $(this).position().top,     { expires: 30, path: "/" });
            console.info('wtp infobox position saved in cookie');
        });


        // init containers BORDER TOGGLER
        WTP_infobox.dblclick(function(){
            $('body.dev').toggleClass('wtp-borders');

            // save borders visibility
            $.cookie("wtp-borders-enabled",
                $('body.dev').hasClass('wtp-borders'), { expires: 30, path: "/" });
            console.info('wtp template borders visibility toggle');
        });

        // restore borders visibility
        var bordersEnabled = $.cookie("wtp-borders-enabled");
        if (bordersEnabled == 'true')   {   $('body.dev').addClass('wtp-borders');  }
        else                            {   $('body.dev').removeClass('wtp-borders'); }
        
        // insert rwd info
        if (wtp.DEV)  {
            WTP_infobox.append('<p class="rwdinfo">');

            var insertRWDinfo = function() {
                    $('body').css('overflow', 'hidden');    // for correct measure without scrollbars
                var width = $( window ).width();
                    $('body').css('overflow', 'auto');
                var widthClass = '';
                if      (width >= 1920)		widthClass = 'lg, hd';
                else if (width >= 1200)	widthClass = 'lg';
                else if (width >= 992)		widthClass = 'md';
                else if (width >= 768)		widthClass = 'sm';
                else if (width >= 480)		widthClass = 'xs';
                else if (width < 480)       widthClass = 'Ys';

                var notice = 'Width: <b>' + width + '</b>, Class: <b>' +widthClass+ '</b>';

                WTP_infobox.find('.rwdinfo').html(notice);
            };

            $( window ).load( insertRWDinfo );
            $( window ).resize( insertRWDinfo );
        }
    },
    
    // checked - from db. todo: check ts & css / create ce
    /*cookieAgree: function(el) {
        $('#cookieMessage').hide();
        $.cookie('cookie_confirmed', true, {
            expires: 90,
            path: "/"
        });
    },

    cookieMessageShow: function() {
        // check for cookie and show
        if (!$.cookie("cookie_confirmed"))      $('#cookieMessage').show();
    },*/

	// enable bootstrap tables, no-ts method
    initTables: function()  {
        //$('.contenttable').addClass('table');
        $('.content table').addClass('table');
        $('.table').wrap('<div class="table-responsive"></div>');
    },


    // focus login form
    initLoginForm: function()   {
        //$('#top-loginbox').click( function(){
        $('#user').focus();
        //});
    },


    // various content manipulation (non-responsive thing)
    moveContentsBetweenContainers: function()   {
        // moveNewsRelations to left column
        $('aside#relations').appendTo( $('#main-content-B') );

        // move frame box above-align image before box header h3
        $('.frame-box > .csc-textpic-above .csc-textpic-imagewrap').each(function(){
            $(this).parent().parent().find('.csc-header').prepend(this);
        });
    },


		/**
		* kontrola szerokosci - przy przejsciu na nizsze layouty responsive przerzucamy niektore bloki w inne miejsca i z powrotem
		* 
		* @param e event
		*/
		/*listenWidth: function( e ) {
			// console.log($(window).width());
			// upewnic sie, ze to nie liczy razem z paskiem przewijania!
			if($(window).width()<975)		// is this width correct? is this working as expected in all browsers?
			{
				// apla - first move it to container, then try to move before slider. because there's no slider on subpages 
				$(".apla").prependTo( $("#main-before") );
				$(".apla").insertBefore( $("#news-tab") );
				// move meta menu after main menu ul
				$("#page-meta ul").addClass('meta').insertAfter( $("#page-menu ul") );
			} else {
				// restore apla
				$(".apla").insertBefore( $(".latest-list") );
				// restore meta menu
				$("#page-menu ul.meta").appendTo( $("#page-meta") );
			}

			//jQuery(window).height(); // New height
			//jQuery(window).width(); // New width
		},*/


	/**
	*  standard typo3 anchors includes domain and/or page.
	*  for Fancybox open ce we need anchors alone.
	*  this should be before fancybox apply.
	*/
	fixFancyboxAnchors: function()	{
		$('.fancybox').each(function(index) {
			var anchor = $(this).attr('href').split('#')[1];
			if (anchor)
			$(this).attr('href', "#" + anchor);
		});
	},
	
	fixDownloads: function()	{
		$('.csc-uploads-fileName').each(function(){
			$(this).find('.csc-uploads-description').wrapInner('<a href="'+  $(this).find('a').prop('href')  +'"/>');
		});
	},

	equalizeColumns: function()	{
		var padding = 66+2;
		var col1H = $('.lay-sub-2col .col-1').outerHeight();
		var col2H = $('.lay-sub-2col .col-2').outerHeight();
//		console.log(col2H);
		if (col1H > col2H)	{
			$('.lay-sub-2col .col-2').css('height', col1H-padding);
		}
		else if (col2H > col1H)	{
			$('.lay-sub-2col .col-1').css('height', col2H-padding);
		}
	},

		/*checkIfHideAgeSplash: function()	{
			// show form layer - because it starts with visible overlay and hidden form to avoid form blink on start
			$('.agesplash-inner').css('display', 'block');
			
			// if confirmed, remove all splash layers
			if ($.cookie("age_confirmed"))	{
				wtp.hideAgeSplash(false, true);
			}
		},*/


		/**
		*  hide age splash form. can be after submit - with animation,
		*  or on start - without it
		*/
		hideAgeSplash: function(setCookie, noAnimation)	{
			if (setCookie)
				$.cookie("age_confirmed", "1", {
						expires: 30,
						path: "/"
				});

			if (noAnimation)	{
				//console.log('splash - hiding noanim');
				$('.agesplash-overlay').remove();
				$('.agesplash-inner').remove();
				return;
			}

			// hide splash		
			$('.agesplash-overlay').animate({
				opacity: 0
			}, 300, function() {
				$('.agesplash-overlay').remove();
			});
			$('.agesplash-inner').animate({
				opacity: 0
			}, 300, function() {
				$('.agesplash-inner').remove();
			});

		},

		/* move to outside helper js */
	
	
		/**
		*  jezdzacy punkt pod pozycjami menu, ustawiajacy sie na aktywnej / hover
		*/
		makeMenuSlidingMarker: function()	{
			// okreslamy polozenie markera
			var marker_x = wtp.findMenuMarkerPosition('.act');

			// jesli x=0, znaczy zadna nie jest aktywna lub blad
			if (!marker_x)	marker_x = -20;
			$('#menu-marker').css("display", "block");

			// ustawiamy marker na biezacej
			$('#menu-marker').css("marginLeft", marker_x+"px");

			$('.level-1 > li').hover(
			  function () {
				// przesuwamy
				$('#menu-marker').animate({
					marginLeft:  wtp.findMenuMarkerPosition(this)+"px"
				}, {duration: 500, queue: false}, function() {});

				// kolor hover
				$('#menu-marker').css("backgroundPosition", "0 -11px");
			  },
			  
			  function () {
				// wracamy
				$('#menu-marker').animate({
					marginLeft:  marker_x
				}, {duration: 500, queue: false}, function() {});


				// zwykly kolor
				$('#menu-marker').css("backgroundPosition", "0 0");
			  }
			);
		},

		
		// szukamy naszego obiektu (pozycji menu) i liczymy szerokosci poprzednich + 0.5 jego (srodek)
		findMenuMarkerPosition: function(obj)	{
			var x = 0;
			var found = false;
			$( $('.level-1 > li') ).each(function(index) {
				// jesli znaleziono wybrany
				if ($(this).is(obj))	{
					// get half its width and exit
					x += $(this).width() / 2;
					found = true;
					return false;
				}
				// szukamy dalej
				x += $(this).outerWidth();// + wtp.menuPositionMargin;
			});
			if (!found)	return 0;
			// korekta przesuniecia - na srodek pozycji
			x -= $('#menu-marker').width() / 2;
			return x;
		},
	
	
		breakLongMenuItems: function(length)	{
			// Line Splitter Function
			// copyright Stephen Chapman, 19th April 2006
			// you may copy this code but please keep the copyright notice as well
			splitLine = function splitLine(st, n) {var b = ''; var s = st;while (s.length > n) {var c = s.substring(0,n);var d = c.lastIndexOf(' ');var e =c.lastIndexOf('\n');if (e != -1) d = e; if (d == -1) d = n; b += c.substring(0,d) + '\n';s = s.substring(d+1);}return b+s;};
			$( '#menu-main > ul > li > a' ).each(function(index) {
				$(this).html(   splitLine(jw(this).html(), length).replace(/\n/g,'<br>')    );
			});
		},
	
	
		// dodajemy koncowki do buttonow
		makeMoreButtonEndings: function()	{
			$( '.more' ).each(function(index) {
				$(this).css('position', 'relative');
				var height = $(this).outerHeight();
				$(this).html('<span class="buttontext">'+$(this).html()+'</span><span class="end" style="height:'+height+'px; width:1px; position:absolute; top:0; right:0;"></span>')
			});
		},

		makeMoreButtonFromSubmit: function()	{
			$( '.tx-powermail-pi1 input[type=submit], #subscribe_box input[type=submit]' ).each(function(index) {
				var button = $(this);
				var more = $('<a class="more" href="">'+button.val()+'</a>')
					.click(function(){button.click(); return false;})
					.appendTo(button.parent());
				button.css('display', 'none');
			});
		}
}


// todo: czym to sie rozni od sociala? nie mozna by tego ujednolicic i zrobic wspolnego?

if (!wtpAjax) var wtpAjax = {

    ajax_url: '',

    // can be set, but not neccessary
    base_url: '',

    //DEV: true,
    DEV: wtp.DEV,

    /**
     * AJAX
     */

    /**
     * load-more buttons
     * @param url - pass params as already built url, to enable chash
     * @param caller - button/anchor js element
     * @param specifiedContainer - can be set to load data into that element (and also animate it)
     * @param replaceContent - don't append new items, replace old
     * @param confOptions - array additional settings
     */
    getResults: function(url, caller, specifiedContainer, successFunc, replaceContent, confOptions)	{
        //console.log(url, 'URL');
        //console.log(conf, 'CONF');
        var trigger = $(caller).parent();   // button wrap
        var container;
        //  console.log(specifiedContainer, 'specifiedContainer');
        if (specifiedContainer)     container = $(specifiedContainer);
        // get first occurance of container 2 levels up (closest
        else                        container = $(caller).parent().parent().find('> .items-container');
        //else                        var container = $(caller).closest('.items-container');    // why this isn't working?
        //console.log(caller, 'caller - load trigger');
        if (wtpAjax.DEV)    console.log(container, 'CONTAINER TO LOAD INTO');
        //	container.css('border', '1px solid red');

        wtpAjax.animationStart(container);
        //$(inner).fadeTo('fast', 0.3);
        var conf = {
            // pass this in url, to make chash
            //data: {ajaxType: 'getResults', controller: controller, offset: offset},
            successCallback: function(response) {
                try {
                    // if there are problems with disabling some debug comments, use this.
                    //var result = $.parseJSON(response.split('<!-- Parsetime')[0]);
                    //console.log(response, 'response');
                    // dont need to parse when sending pure json
                    //var result = $.parseJSON(response);
                    //console.log(result, 'result');

                    // if needed, clear container
                    if (replaceContent)     $(container).html('');

                    // PUT ITEMS INTO CONTAINER
                    //$(container).append(result.res);
                    $(container).append(response.res);

                    /*// ANIMATED VERSION
                    // put new items into dom (temporary container), start animation and then put into right place
                    var temp_container = $('<div class="temp hidden">');
                    temp_container.append( $(result.res) );
                    $(container).append( temp_container );
                    wtpAjax.animateItemsLoad( $(container).find('.temp > .item') );
                    //$(container).append( $(container).find('.temp > .item') );

                    if (confOptions && confOptions.loadMode == 'prepend')
                        $(container).prepend( $(container).find('.temp > *') );
                    else
                        $(container).append( $(container).find('.temp > *') );
                    temp_container.remove();*/

                    if (successFunc)    successFunc(response, container);    // custom success function may be passed to call here

                    // stop animation
                    wtpAjax.animationStop(container);

                    //if (!result.res && !result.errors.length)      $(caller).parent().html('<p>Ajax error.</p>');
                } catch (e) {
                    //wtpAjax.handleError( e.message, e );
                    wtpAjax.animationStop(container);
                    console.error(e.message);
                }
                //console.log(result, 'result of request:');
            }
        };
        wtpAjax.request(conf, url, container, false);
    },


    request: function(conf, url, boxToAnimate, nocache)	{
        //console.log(conf, 'request conf');
        //console.log(url, 'custom request url');
        //console.log(wtpAjax.base_url, 'wtpAjax.base_url');
        //console.log(wtpAjax.ajax_url, 'wtpAjax.ajax_url');
        //console.log(this.base_url + (url?url:this.ajax_url) + '&no_debug=1', 'URL_FINAL!!!');
        var request = $.ajax({
            type:       "GET",
            //url:              'http://wtp.local/'+ url?url:this.ajax_url,
            //url:              this.base_url + url ? url : this.ajax_url,
            //url:            wtpAjax.base_url + (url ? url : wtpAjax.ajax_url) + '&no_debug=1' + (nocache ? '&no_cache=1' : ''),
            url:            wtpAjax.base_url + (url ? url : wtpAjax.ajax_url) + (nocache ? '&no_cache=1' : ''),
            //                  data: 'tx_wsocial_pi1[month_ajax]='+month,
            //                  data: { id : menuId },
            data:           conf.data ? conf.data : {}
            //                  dataType: "json",
            //                  dataType: "html"
            //success:
        })

            // this default method is not used, is overwritten in conf.successCallback
            .done(conf.successCallback ? conf.successCallback : function(res) {
                console.info('done!');
                if(parseInt(res)!=0)  {  // if no errors
                    wtpAjax.animationStop(boxToAnimate);
                }
            })
            .fail(function( jqXHR, textStatus ) {
                console.error( "Request failed: " + textStatus );
                //wtpAjax.handleError( "Request failed: " + textStatus );
            });
    },
    
    
    /**
     * load-more buttons
     * @param caller - button/anchor js element
     * @param code - view display mode
     * @param string params - additional params
     * @param specifiedContainer - should be always given, if not result could be unexpected
     * @param function successFunc
     * @param bool replaceContent - don't append new items, replace old
     * @param confOptions - array additional settings. not used for now
     */
    ttnews_getView: function(caller, code, uid, params, specifiedContainer, successFunc, replaceContent, confOptions) {
        //console.log(successFunc);
        if (!params)            params = '';
        //if (!displayMode)       displayMode = controller;
        replaceContent = true;  // i doubt it will ever be needed for anything. getView should always replace whole content, I think. but maybe not in some cases?
        // ten url udaje cached, ma cHash, ale nie zadziala jako cachowany - bo recznie dodajemy parametry. wiec (teoretycznie) zawsze zaciagnie swieze dane.
        // przewaga nad ajax_url jest brak no_cache
        //var url = wtpAjax.ajax_url + '&no_debug=1&tx_ttnews[cat]=getView&tx_wsocial_pi1[controller]='+controller+'&tx_wsocial_pi1[displayMode]='+displayMode + params;
        var url = wtpAjax.ajax_url + '&no_debug=1&'+ params;
        wtpAjax.getResults(url, caller, specifiedContainer, successFunc, replaceContent );

        // set cookie
        $.cookie("last_news_category", uid, {
            expires: 1,
            path: "/"
        });
    },

    /**
     * activate clicked button
     * @param el
     */
    controlAjaxCatButtons: function(el)    {
        $(el).parent().siblings().removeClass('news-catmenu-ACT').addClass('news-catmenu-NO');
        $(el).parent().addClass('news-catmenu-ACT');
    },



    //
    // ANIMATION

    animationStart: function(container)	{
        container.addClass('loading')
            .animate({
                opacity: 0.6
            }, 200, function() {
                // Animation complete.
            });
    },

    animationStop: function(container)	{
        container.removeClass('loading')
            .animate({
                opacity: 1
            }, 200, function() {
                // Animation complete.
            });
    }
}



/**
 * header / winieta / naglowek - shrink at scroll
 */
/*$(window).scroll(function () {
  if ($(document).scrollTop() == 0) {
    $('#page-head').removeClass('tiny');
    $('.addthis-smartlayers-desktop').removeClass('tiny');
  } else {
    $('#page-head').addClass('tiny');
    $('.addthis-smartlayers-desktop').addClass('tiny');
  }
});*/



/* 1. dom ready (same as $(function(){} ) */
$(document).ready(function() {
    wtp.init();
	//wtp.initMenu();
	//wtp.listenWidth(null);
});

/* 2. resources loaded page rendered and assume sizes calculated - ready for resizing actions */
$(window).load(function() {
	/*wtp.initHeaders();
	wtp.equalizeProducts();
	wtp.equalizeColumns();*/
    //wtp.initWTPinfobox();
	/*setTimeout( function() {
            wtp.initWTPinfobox();
		}, 400
		);*/
});


//$(window).bind("resize", wtp.listenWidth);
