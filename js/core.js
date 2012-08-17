function pin(el) {
	var filters = document.getElementById('filters');
	if (el.checked) {
		filters.className = 'pin';
	} else {
		filters.className = 'floating';
	}
}
(function () {
	window.onload = function () {
		var f = document.getElementById('pin');
		pin(f);
		f.onclick = function () {
			pin(f);
		}
	};
})();