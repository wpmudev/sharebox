(function ($) {


var $box = $("#wdsb-share-box");
var $win = $(window);
var minTop = 0;
var _wdsb_left_offset = 0;

var topLimit = 0;
var bottomLimit = 0;

$(function () {

	$box = $("#wdsb-share-box");
    if (!$box.length) return false;
    $box.next("p:empty").remove(); // Compensate for wpautop

    setLeftOffset();

    $win.resize(function () {
		if (_wdsb_data.min_width && $win.width() < _wdsb_data.min_width) {
		// Min width
			$box.removeClass("wdsb-has_message");
			$box.addClass('wdqs-inline');
			$win.unbind('scroll', scrollDispatcher);
		} else if (_wdsb_data.is_singular && _wdsb_data.min_post_height && $box.parent().height() <  _wdsb_data.min_post_height) {
		// Min post height
			$box.removeClass("wdsb-has_message");
			$box.addClass('wdqs-inline');
			$win.unbind('scroll', scrollDispatcher);
		} else {
			$box.removeClass('wdqs-inline');
			init();
		}
    });

	// Check for minimum width right away
    if (_wdsb_data.min_width && $win.width() < _wdsb_data.min_width) {
	// Min width
		$box.removeClass("wdsb-has_message");
		$box.addClass('wdqs-inline');
		return;
    } else if (_wdsb_data.is_singular && _wdsb_data.min_post_height && $box.parent().height() <  _wdsb_data.min_post_height) {
    // Min post height
		$box.removeClass("wdsb-has_message");
		$box.addClass('wdqs-inline');
		return;
    } else {
		if ($box.find('iframe').length) $box.find('iframe').load(init);
		else $win.load(init);
		// Try repositioning after a while
		$win.load(function () { init(); setTimeout(init, 2000); });
    }
});

function setLeftOffset () {
	if ("text" == _wdsb_data.offset.htype) {
		_wdsb_left_offset = ("left" == _wdsb_data.offset.hdir) ?
			$box.parent().offset().left - ($box.outerWidth() + _wdsb_data.offset.hoffset)
			:
			($box.parent().offset().left + $box.parent().width()) - _wdsb_data.offset.hoffset
		;
	} else if ("page" == _wdsb_data.offset.htype) {
		_wdsb_left_offset = ("left" == _wdsb_data.offset.hdir) ?
			_wdsb_data.offset.hoffset
			:
			$win.width() - ($box.outerWidth() + _wdsb_data.offset.hoffset)
		;
	} else {
		_wdsb_left_offset = ("left" == _wdsb_data.offset.hdir) ?
			$(_wdsb_data.horizontal_selector).offset().left - ($box.outerWidth() + _wdsb_data.offset.hoffset)
			:
			($(_wdsb_data.horizontal_selector).offset().left + $(_wdsb_data.horizontal_selector).width()) - _wdsb_data.offset.hoffset
		;
	}
}

function setTopOffset () {
	if ("page-bottom" == _wdsb_data.offset.vtype) {
		minTop = $win.height() - ($box.outerHeight() + _wdsb_data.offset.voffset);
	} else if ("page-top" == _wdsb_data.offset.vtype) {
		minTop = _wdsb_data.offset.voffset;
	} else if ("text" == _wdsb_data.offset.vtype) {
		minTop = $box.parent().offset().top + _wdsb_data.offset.voffset;
	} else if ("selector" == _wdsb_data.offset.vtype) {
		minTop = $(_wdsb_data.top_selector).offset().top + _wdsb_data.offset.voffset;
	}
	// Set limits
	if (minTop < topLimit) minTop = topLimit;
}

function setTopLimit () {
	var $top = $(_wdsb_data.limit.top_selector);
	var top = $top.length ? ($top.offset().top + $top.height()) : 0;
	top += _wdsb_data.limit.top_offset;
	topLimit = top;

	var $bottom = $(_wdsb_data.limit.bottom_selector);
	var bottom = $bottom.length ? $bottom.offset().top : $(document).height();
	bottomLimit = bottom - (_wdsb_data.limit.bottom_offset + $box.height());
}

function init () {
	if ($box.is(".wdqs-inline")) return;
	if ($win.height() < $box.height()) {
		$box.addClass('wdqs-inline');
		$win.unbind('scroll', scrollDispatcher);
		return;
	}

	$box.removeClass("wdsb-has_message");
	if (transformSupported()) {
		var $msg = $box.find(".wdsb-text_message");
		if ($msg.length) {
			$msg.css('display', 'inline-block');
			$msg.css('min-width', $box.css('height'));
			$box.addClass("wdsb-has_message");
		}
	}

	// Calculate minimum top
	setTopLimit();
	setTopOffset();
	setLeftOffset();

	// Position the box first
    $box.css({
		"display": "block",
		"z-index": parseInt(_wdsb_data.z_index, 10),
		"position": (($.browser && $.browser.msie && !_wdsb_data.allow_fixed) ? "absolute" : "fixed")
    });
    assignDimensions();
    scrollDispatcher();
    $win.unbind('scroll', scrollDispatcher).bind('scroll', scrollDispatcher);
}

function scrollDispatcher () {
	var vPos = $win.scrollTop();
	if ($("#wpadminbar").length) vPos += $("#wpadminbar").height();
	if ($("#wp-admin-bar").length) vPos += $("#wp-admin-bar").height();
	if (vPos > minTop) {
		if (vPos > topLimit && vPos < bottomLimit) {
			$box.offset({
				"top": vPos + _wdsb_data.limit.top_offset,
				"left": _wdsb_left_offset
			});
		} else if (vPos > bottomLimit) {
			$box.offset({
				"top": bottomLimit,
				"left": _wdsb_left_offset
			});
		} else if (vPos < topLimit) {
			$box.offset({
				"top": topLimit,
				"left": _wdsb_left_offset
			});
		}
	} else {
		$box.offset({
			"top": minTop,
			"left": _wdsb_left_offset
        });
	}
}

function assignDimensions () {
	if ( !$box.hasClass( 'wdqs-inline' )) {
		var socWidths = [];
		$box.find( 'li' ).each( function(){
			// if iframe in mark-up, assing it's dimensions to container ( for css centering )
			if ( $( this ).find( 'iframe' ).length ) {
				var iframe = $( this ).find( 'iframe' ),
					target = $( this ).find( 'div.wdsb-item' );
				if (iframe.width() > $box.width()) return true;
				target.width( iframe.width() ).height( iframe.height() );

			}

		});

		$box.find( '.wdsb-item' ).each( function(){
			socWidths.push( $(this).width() );
		});

		$box.css('min-width', Math.max.apply( Math, socWidths )+ 'px' );

	}
}

function transformSupported () {
	var pfx = 'transform WebkitTransform MozTransform OTransform msTransform'.split(' '),
		div = document.createElement('div')
	;
	for (var i = 0; i < pfx.length; i++) {
		if (div.style[pfx[i]] !== undefined) return true;
	}
	return false;
}

})(jQuery);