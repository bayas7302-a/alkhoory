/* Yutong UAE: quote form helpers.
 * - "Model of Interest" only lists models of the chosen "Vehicle Category".
 * - ?model=<name> in the URL (from "Request a Quote" links) pre-selects the model and its category.
 */
(function () {
	var CATEGORY = {
		'Coaches': ['C9', 'C11', 'C12 Pro', 'D7'],
		'E-Coaches': ['C9E', 'C12E', 'D8E', 'E11', 'IC12E', 'T12E'],
		'City Buses': ['ZK6126HG', 'ZK6810HG', 'ZK6890HGQ'],
		'E-Buses': ['U18', 'U12', 'E7S', 'E9', 'E12'],
		'Vans': ['V6', 'V6E']
	};

	function categoryOf(model) {
		for (var c in CATEGORY) {
			if (CATEGORY[c].indexOf(model) !== -1) { return c; }
		}
		return '';
	}

	function setup(form) {
		var cat = form.querySelector('select.yt-category-select');
		var model = form.querySelector('select.yt-model-select');
		if (!model) { return; }
		var all = Array.prototype.map.call(model.options, function (o) { return { value: o.value, text: o.text }; });

		function filter() {
			var allowed = cat && CATEGORY[cat.value];
			var current = model.value;
			model.innerHTML = '';
			all.forEach(function (o) {
				var keep = !o.value || !allowed || allowed.indexOf(o.value) !== -1 || /not sure/i.test(o.value);
				if (keep) {
					var opt = document.createElement('option');
					opt.value = o.value;
					opt.text = o.text;
					model.appendChild(opt);
				}
			});
			model.value = Array.prototype.some.call(model.options, function (o) { return o.value === current; }) ? current : '';
		}

		var wanted = new URLSearchParams(window.location.search).get('model');
		if (wanted) {
			var c = categoryOf(wanted);
			if (cat && c) { cat.value = c; }
			if (cat) { filter(); }
			model.value = wanted;
		}
		if (cat) { cat.addEventListener('change', filter); }
	}

	document.addEventListener('DOMContentLoaded', function () {
		Array.prototype.forEach.call(document.querySelectorAll('.wpcf7 form'), setup);
	});
})();
