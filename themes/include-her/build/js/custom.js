//CUSTOM.JS

window.addEventListener('load', () => {
	(function(){
		if($("body").hasClass("events")){
			$("#member-events-upcoming-personal-li").addClass("blueCurrent");
		}
		else{
			return 0;
		}
	});
});

window.addEventListener('load', () => {
	(function() {
		[].slice.call(document.querySelectorAll('.navbar-collapse')).forEach(function(menu) {
			var menuItems = menu.querySelectorAll('.nav-link'),
				setCurrent = function(ev) {
					//ev.preventDefault();
					var item = ev.target.parentNode; // li
					// return if already current
					if (classie.has(item, 'nav-active')) {
						return false;
					}
					// remove current
					classie.remove(menu.querySelector('.nav-active'), 'nav-active');
					// set current
					classie.add(item, 'nav-active');
				};
			[].slice.call(menuItems).forEach(function(el) {
				el.addEventListener('click', setCurrent);
			});
		});
		/*[].slice.call(document.querySelectorAll('.link-copy')).forEach(function(link) {
			link.setAttribute('data-clipboard-text', location.protocol + '//' + location.host + location.pathname + '#' + link.parentNode.id);
			new Clipboard(link);
			link.addEventListener('click', function() {
				classie.add(link, 'link-copy--animate');
				setTimeout(function() {
					classie.remove(link, 'link-copy--animate');
				}, 300);
			});
		});*/
	})(window);
});







