(function ($, undefined) {

/*
function fetch_stats_for (service, url, $root) {
	url = 'http://xkcd.com/792/';
	var services = {
		//"linkedin": "http://www.linkedin.com/countserv/count/share?format=json&url=" + encodeURIComponent(url), // ????
		"twitter": "http://urls.api.twitter.com/1/urls/count.json?url=" + url + "&callback=?",
		"facebook": "https://graph.facebook.com/" + url + "&callback=?"
	}
	if (!services[service]) return false;

	$.getJSON(services[service], function (data) {
		var count = 'N/A';
		if ("facebook" == service && data.shares) count = data.shares;
		else if ("twitter" == service && data.count) count = data.count;
else console.log([service, data])
		$root.html(count);
	});
}

function init () {
	$("#wdsb-stats tbody .wdsb-service").each(function () {
		var $me = $(this),
			service = $me.attr("data-service"),
			url = $me.attr("data-url")
		;
		fetch_stats_for(service, url, $me);
	});
}

$(init);
*/
})(jQuery);