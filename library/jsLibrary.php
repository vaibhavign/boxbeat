<?php

// Path to Jquery Bundle
// Here I have kept all the development bundle on root in jquery-ui folder
// set the jquery bundle path
define('JUI_BUNDLE', '/jquery-ui/');

class JsLibrary {

    public $view;
    

    public function __construct() {
        $this->view = '<link rel="stylesheet" href="' . JUI_BUNDLE . 'themes/base/jquery.ui.all.css">
            <script src="' . JUI_BUNDLE . 'ui/jquery.ui.core.js"></script>
            <script src="' . JUI_BUNDLE . 'ui/jquery.ui.widget.js"></script>
                <link rel="stylesheet" href="' . JUI_BUNDLE . 'demos/demos.css">';
        
    }

    public function accordion($content, $type=0) {
        $this->view.= '<script src="' . JUI_BUNDLE . 'ui/jquery.ui.accordion.js"></script>';

        switch ($type) {
            case 1:
                //Fill Space
                $this->view.='<script src="' . JUI_BUNDLE . 'ui/jquery.ui.mouse.js"></script>
                        <script src="' . JUI_BUNDLE . 'ui/jquery.ui.resizable.js"></script>';
                $this->view.='<script>
	$(function() {
		$( "#accordion" ).accordion({
			fillSpace: true,
                         header: "h3"
		});
	});
	$(function() {
		$( "#accordionResizer" ).resizable({
			minHeight: 140,
			resize: function() {
				$( "#accordion" ).accordion( "resize" );
			}
		});
	});
	</script>';
                break;
            case 2:
                //No Auto Height
                $this->view.='<script>
	$(function() {
		$( "#accordion" ).accordion({
			autoHeight: false,
			navigation: true
		});
	});
	</script>';
                break;
            case 3:
                //Collapse Content
                $this->view.='<script>
	$(function() {
		$( "#accordion" ).accordion({
			collapsible: true
		});
	});
	</script>';
                break;
            case 4:
                $this->view.='<script>
	$(function() {
		$( "#accordion" ).accordion({
			event: "mouseover"
		});
	});
	</script>';
                break;
            case 5:
                //Open On Hoverindent
                $this->view.='<script>
	$(function() {
		$("#accordion").accordion({
			event: "click hoverintent"
		});
	});
	
	var cfg = ($.hoverintent = {
		sensitivity: 7,
		interval: 100
	});

	$.event.special.hoverintent = {
		setup: function() {
			$( this ).bind( "mouseover", jQuery.event.special.hoverintent.handler );
		},
		teardown: function() {
			$( this ).unbind( "mouseover", jQuery.event.special.hoverintent.handler );
		},
		handler: function( event ) {
			event.type = "hoverintent";
			var self = this,
				args = arguments,
				target = $( event.target ),
				cX, cY, pX, pY;
			
			function track( event ) {
				cX = event.pageX;
				cY = event.pageY;
			};
			pX = event.pageX;
			pY = event.pageY;
			function clear() {
				target
					.unbind( "mousemove", track )
					.unbind( "mouseout", arguments.callee );
				clearTimeout( timeout );
			}
			function handler() {
				if ( ( Math.abs( pX - cX ) + Math.abs( pY - cY ) ) < cfg.sensitivity ) {
					clear();
					jQuery.event.handle.apply( self, args );
				} else {
					pX = cX;
					pY = cY;
					timeout = setTimeout( handler, cfg.interval );
				}
			}
			var timeout = setTimeout( handler, cfg.interval );
			target.mousemove( track ).mouseout( clear );
			return true;
		}
	};
	</script>';
                break;
            case 6:
                $this->view.='<script src="' . JUI_BUNDLE . 'ui/jquery.ui.button.js"></script>
	<script>
	$(function() {
		var icons = {
			header: "ui-icon-circle-arrow-e",
			headerSelected: "ui-icon-circle-arrow-s"
		};
		$( "#accordion" ).accordion({
			icons: icons
		});
		$( "#toggle" ).button().toggle(function() {
			$( "#accordion" ).accordion( "option", "icons", false );
		}, function() {
			$( "#accordion" ).accordion( "option", "icons", icons );
		});
	});
	</script>';
                break;
            case 7:
                $this->view.='<script src="' . JUI_BUNDLE . 'ui/jquery.ui.mouse.js"></script>
	<script src="' . JUI_BUNDLE . 'ui/jquery.ui.sortable.js"></script>
	
	<script>
	$(function() {
		var stop = false;
		$( "#accordion h3" ).click(function( event ) {
			if ( stop ) {
				event.stopImmediatePropagation();
				event.preventDefault();
				stop = false;
			}
		});
		$( "#accordion" )
			.accordion({
				header: "> div > h3"
			})
			.sortable({
				axis: "y",
				handle: "h3",
				stop: function() {
					stop = true;
				}
			});
	});
	</script>';
                break;
            default:
                $this->view.='<script>
	$(function() {
		$( "#accordion" ).accordion();
	});
	</script>';
        }

        $this->view.='<link rel="stylesheet" href="' . JUI_BUNDLE . 'demos/demos.css">';

        $this->view.='<div class="demo"><div id="accordion">';
        foreach ($content as $item) {
            $this->view.= '
                   <h3><a href="#">' . $item['header'] . '</a></h3>
                   <div>
                       <p>' . $item['content'] . '</p>
                  </div>';
        }

        $this->view.='</div></div>';
        return$this->view;
    }

    public function autocomplete($items, $name='auto_comp',$class=NULL, $type=0,$default=NULL) {

        $this->view.='

	<script src="' . JUI_BUNDLE . 'ui/jquery.ui.position.js"></script>
	<script src="' . JUI_BUNDLE . 'ui/jquery.ui.autocomplete.js"></script>
	
        <script src="' . JUI_BUNDLE . 'ui/jquery.ui.button.js"></script>';
        switch ($type) {
            case 1:
                // Scrollable Result
                $this->view.='<style>
	.ui-autocomplete {
		max-height: 100px;
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
		/* add padding to account for vertical scrollbar */
		padding-right: 20px;
	}
	
	* html .ui-autocomplete {
		height: 100px;
	}
	</style>';
                break;
            case 2:
                $this->view.='<style>
	.ui-button { margin-left: -1px; }
	.ui-button-icon-only .ui-button-text { padding: 0.35em; } 
	.ui-autocomplete-input { margin: 0; padding: 0.48em 0 0.47em 0.45em; }
	</style>
	<script>
	(function( $ ) {
		$.widget( "ui.combobox", {
			_create: function() {
				var self = this,
					select = this.element.hide(),
					selected = select.children( ":selected" ),
					value = selected.val() ? selected.text() : "";
				var input = this.input = $( "<input>" )
					.insertAfter( select )
					.val( value )
					.autocomplete({
						delay: 0,
						minLength: 0,
						source: function( request, response ) {
							var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
							response( select.children( "option" ).map(function() {
								var text = $( this ).text();
								if ( this.value && ( !request.term || matcher.test(text) ) )
									return {
										label: text.replace(
											new RegExp(
												"(?![^&;]+;)(?!<[^<>]*)(" +
												$.ui.autocomplete.escapeRegex(request.term) +
												")(?![^<>]*>)(?![^&;]+;)", "gi"
											), "<strong>$1</strong>" ),
										value: text,
										option: this
									};
							}) );
						},
						select: function( event, ui ) {
							ui.item.option.selected = true;
							self._trigger( "selected", event, {
								item: ui.item.option
							});
						},
						change: function( event, ui ) {
							if ( !ui.item ) {
								var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
									valid = false;
								select.children( "option" ).each(function() {
									if ( $( this ).text().match( matcher ) ) {
										this.selected = valid = true;
										return false;
									}
								});
								if ( !valid ) {
									/*remove invalid value, as it didnt match anything*/
									$( this ).val( "" );
									select.val( "" );
									input.data( "autocomplete" ).term = "";
									return false;
								}
							}
						}
					})
					.addClass( "ui-widget ui-widget-content ui-corner-left" );

				input.data( "autocomplete" )._renderItem = function( ul, item ) {
					return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( "<a>" + item.label + "</a>" )
						.appendTo( ul );
				};

				this.button = $( "<button type=\'button\'>&nbsp;</button>" )
					.attr( "tabIndex", -1 )
					.attr( "title", "Show All Items" )
					.insertAfter( input )
					.button({
						icons: {
							primary: "ui-icon-triangle-1-s"
						},
						text: false
					})
					.removeClass( "ui-corner-all" )
					.addClass( "ui-corner-right ui-button-icon" )
					.click(function() {
						// close if already visible
						if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
							input.autocomplete( "close" );
							return;
						}

						// work around a bug (likely same cause as #5265)
						$( this ).blur();

						// pass empty string as value to search for, displaying all results
						input.autocomplete( "search", "" );
						input.focus();
					});
			},

			destroy: function() {
				this.input.remove();
				this.button.remove();
				this.element.show();
				$.Widget.prototype.destroy.call( this );
			}
		});
	})( jQuery );

	$(function() {
		$( "#combobox" ).combobox();
		$( "#toggle" ).click(function() {
			$( "#combobox" ).toggle();
		});
	});
	</script><div class="demo">

<div class="ui-widget">
	
	<select id="combobox" name="' . $name . '">';
                foreach ($items as $key => $value) {
                    $this->view.='<option value="' . $key . '">' . $value . '</option>';
                }
                $this->view.='</select>
</div>';
                break;

            case 3:
                // print_r($items);
                $jsonstring;
                while (list($key, $value) = each($items)) {
                    $category = $key;
                    while (list($key1, $value1) = each($value)) {
                        $jsonstring.='{ label: "' . $value1 . '", category: "' . $category . '" },';
                    }
                }

                $this->view.= '<style>
	.ui-autocomplete-category {
		font-weight: bold;
		padding: .2em .4em;
		margin: .8em 0 .2em;
		line-height: 1.5;
	}
	</style>
	<script>
	$.widget( "custom.catcomplete", $.ui.autocomplete, {
		_renderMenu: function( ul, items ) {
			var self = this,
				currentCategory = "";
			$.each( items, function( index, item ) {
				if ( item.category != currentCategory ) {
					ul.append( "<li class=\'ui-autocomplete-category\'>" + item.category + "</li>" );
					currentCategory = item.category;
				}
				self._renderItem( ul, item );
			});
		}
	});
	</script>
	<script>
	$(function() {
		var data = [' . substr($jsonstring, 0, -1) . '];
		
		$( "#search" ).catcomplete({
			delay: 0,
			source: data
		});
	});
	</script><div class="demo">
	<label for="search">Search: </label>
	<input id="search" />
</div>';
                break;
            case 4:
                $this->view.=
                        '<script>
	$(function() {
		var availableTags = ' . json_encode(array_values($items)) . '
		function split( val ) {
			return val.split( /,\s*/ );
		}
		function extractLast( term ) {
			return split( term ).pop();
		}

		$( "#tags" )
			// don\'t navigate away from the field on tab when selecting an item
			.bind( "keydown", function( event ) {
				if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				minLength: 0,
				source: function( request, response ) {
					// delegate back to autocomplete, but extract the last term
					response( $.ui.autocomplete.filter(
						availableTags, extractLast( request.term ) ) );
				},
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				select: function( event, ui ) {
					var terms = split( this.value );
					// remove the current input
					terms.pop();
					// add the selected item
					terms.push( ui.item.value );
					// add placeholder to get the comma-and-space at the end
					terms.push( "" );
					this.value = terms.join( ", " );
					return false;
				}
			});
	});
	</script>
</head>
<body>

<div class="demo">

<div class="ui-widget">
	
	<input id="tags" name="' . $name . '" size="50" />
</div>

</div>';




                break;
            default:
                $tags = (count($items)==0)?'':json_encode(array_values($items));
                //echo 'default called';
                $this->view.='<script>
	$(function() {
		var availableTags =' . $tags  . ';
		$( ".my_auto_complete" ).autocomplete({
			source: availableTags
		});
	});
	</script>
        <div class="demo">
            <div class="ui-widget">
                <input class="my_auto_complete searchInput '.$class.'" name=' . $name . ' value="'.$default.'" />
            </div>
        </div> ';
        }
        return $this->view;
    }

    function datepicker($name='datepicker',$attr=NULL, $format='dd-mm-yy', $range=NULL, $type=NULL,$multi=3,$effect=NULL) {
        
        //$effect can be:
        //  1.show
        //  2.slideDown
        //  3.fadeIn
        //  4.blind
        //  5.bounce
        //  6.clip
        //  7.drop
        //  8.fold;
        //  9.slide
      
        $this->view.='<script src="'.JUI_BUNDLE.'ui/jquery.effects.core.js"></script>
	<script src="'.JUI_BUNDLE.'ui/jquery.effects.blind.js"></script>
	<script src="'.JUI_BUNDLE.'ui/jquery.effects.bounce.js"></script>
	<script src="'.JUI_BUNDLE.'ui/jquery.effects.clip.js"></script>
	<script src="'.JUI_BUNDLE.'ui/jquery.effects.drop.js"></script>
	<script src="'.JUI_BUNDLE.'ui/jquery.effects.fold.js"></script>
	<script src="'.JUI_BUNDLE.'ui/jquery.effects.slide.js"></script>';
        
        if ($type == 1) {
            // DISPLAY BUTTON BAR
            $showButtonPanel = true;
        }
        if ($type == 2) {
            // DISPLAY COMBO BOX FOR MONTH AND YEAR
            $changeMonthYear = true;
        }
          if ($type == 3) {
            // SHOW WEEKS NUMBER OF THE YEAR
            $showWeek = true;
        }
         if ($type == 4) {
            // SHOW MULTIPLE DATEPICKERS
            $showMulti = true;
            $multi=$multi;
            
        }
         if ($type == 5) {
            // SHOW ICON
            $showIcon = true;
            
            
        }
         if ($type == 6) {
            // SHOWING DATE RANGE
            $showRange = true;
            
            
        }
        
        $range = substr($range, strpos($range, '[') + 1, strpos($range, ']') - 1);
        $rangeArray = explode(',', $range);
        $minRange = $rangeArray[0];
        $maxRange = $rangeArray[1];
        $newRange = 'minDate: ' . $minRange . ', maxDate: "' . $maxRange . '"';
        $attribute = '{' . 
        ($range != NULL ? $newRange . ',' : '') . 
            ($showButtonPanel ? 'showButtonPanel: true' . ',' : '') . 
            ($changeMonthYear ? 'changeMonth: true,changeYear: true' . ',' : '') .
            ($showWeek?'showWeek: true,firstDay: 1' . ',' : '') .
            ($showMulti?'numberOfMonths: '.$multi . ',' : '') .
            ($showIcon?'showOn: "button",
			buttonImage: "/images/admin/orders/cals.gif",
			buttonImageOnly: true '.',' : '') .
            
            '}';
        $this->view.='<script src="' . JUI_BUNDLE . 'ui/jquery.ui.datepicker.js"></script>';

        $this->view.='<script>
	$(function() {
		 $( "#datepicker" ).datepicker(' . $attribute . ');
		 $( "#datepicker" ).datepicker("option", "dateFormat","' . $format . '");'.
                 ($effect!=NULL?'$( ".datepicker" ).datepicker( "option", "showAnim", "'.$effect.'");':'')
                .'
                
	});
	</script>';
        if($type==6){
            $this->view.='<script>
	$(function() {
		var dates = $( "#from, #to" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: '.$multi.',
			onSelect: function( selectedDate ) {
				var option = this.id == "from" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	});
	</script>';
        }


        $this->view .= '<div class="demo">'.
                (($type==6)?'<label for="from">From</label>
<input type="text" id="from" name="from"/>
<label for="to">to</label>
<input type="text" id="to" name="to"/>':'<input type="text" id="datepicker" name="' . $name . '" '.$attr.'size="30"/>');
        $this->view.='</div>';
        return $this->view;
    }

    function datepickerInline($range=NULL) {
        $range = substr($range, strpos($range, '[') + 1, strpos($range, ']') - 1);
        $rangeArray = explode(',', $range);
        $minRange = $rangeArray[0];
        $maxRange = $rangeArray[1];
        $newRange = '{minDate: ' . $minRange . ', maxDate: "' . $maxRange . '"} ';
        $this->view.='<script src="' . JUI_BUNDLE . 'ui/jquery.ui.datepicker.js"></script><script>
	$(function() {
		$( "#datepicker" ).datepicker(' . ($range != NULL ? $newRange : '') . ');
	});
	</script><div class="demo">

<div id="datepicker"></div>

</div>';
        return $this->view;
    }
    
    
    function tabs($contentArray,$type=NULL){
        
        $this->view.='<script src="' . JUI_BUNDLE . 'ui/jquery.ui.tabs.js"></script>';
        switch($type){
            case 1:
                // On MouseOver
                 $this->view.='<script>
	$(function() {
		$( "#tabs" ).tabs({
			event: "mouseover"
		});
	});
	</script>';
                break;
            case 2:
                // Tabs on bottom Side
                $this->view.='<script>
	$(function() {
		$( "#tabs" ).tabs();
		$( ".tabs-bottom .ui-tabs-nav, .tabs-bottom .ui-tabs-nav > *" )
			.removeClass( "ui-corner-all ui-corner-top" )
			.addClass( "ui-corner-bottom" );
	});
	</script>
	<style>
	
	.tabs-bottom { position: relative; } 
	.tabs-bottom .ui-tabs-panel { height: 140px; overflow: auto; } 
	.tabs-bottom .ui-tabs-nav { position: absolute !important; left: 0; bottom: 0; right:0; padding: 0 0.2em 0.2em 0; } 
	.tabs-bottom .ui-tabs-nav li { margin-top: -2px !important; margin-bottom: 1px !important; border-top: none; border-bottom-width: 1px; }
	.ui-tabs-selected { margin-top: -3px !important; }
	</style>';
                break;
            default:
                $this->view.='<script>
	$(function() {
		$( "#tabs" ).tabs();
	});
	</script>';
        }
$this->view.='<div class="demo">

<div id="tabs">
	<ul>';
        $counter = 1;
        foreach($contentArray as $items){
            $this->view.='<li><a href="#tabs-'.$counter.'">'.$items['header'].'</a></li>';
            $counter++;
        }
	reset($contentArray);
        $counter=1;
	$this->view .='</ul>';
         foreach($contentArray as $items){
            $this->view.='<div id="tabs-'.$counter.'">'.$items['content'].'</div>';
            $counter++;
        }
        return $this->view .='</div></div>';
    }

    function __destruct() {
        $this->view = '';
    }

}

?>
