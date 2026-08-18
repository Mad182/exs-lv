document.addEventListener('DOMContentLoaded', function () {
	// Mobile Sidr Menu for Guests
	var sidr = document.getElementById('sidr-right');
	var swipeMenu = document.getElementById('swipe-menu-responsive');

	function initGuestMobileMenu() {
		if (!sidr && swipeMenu) {
			sidr = document.createElement('div');
			sidr.id = 'sidr-right';
			sidr.className = 'sidr right';
			sidr.style.display = 'none';
			sidr.style.position = 'fixed';
			sidr.style.top = '0';
			sidr.style.right = '-476px';
			sidr.style.height = '100%';
			sidr.style.zIndex = '999999';
			sidr.style.transition = 'right 0.2s ease-in-out';

			var inner = document.createElement('div');
			inner.className = 'sidr-inner';
			inner.innerHTML = swipeMenu.innerHTML;

			var closeBtn = inner.querySelector('.close-this-menu');
			if (closeBtn) {
				closeBtn.classList.add('sidr-class-close-this-menu');
			}

			sidr.appendChild(inner);
			document.body.appendChild(sidr);
		}
	}

	initGuestMobileMenu();

	function openMobileMenu() {
		if (!sidr) initGuestMobileMenu();
		if (!sidr) return;
		sidr.style.display = 'block';
		void sidr.offsetWidth; // Force reflow
		sidr.style.right = '0px';
	}

	function closeMobileMenu() {
		if (!sidr) return;
		sidr.style.right = '-476px';
		setTimeout(function () {
			if (sidr && sidr.style.right !== '0px') {
				sidr.style.display = 'none';
			}
		}, 200);
	}

	// Global delegated click listener for guest interactivity
	document.addEventListener('click', function (e) {
		// 0. Mobile Sidr Menu toggle & Close
		var menuBtn = e.target.closest('#responsive-menu-button');
		if (menuBtn) {
			e.preventDefault();
			if (sidr && sidr.style.display === 'block' && sidr.style.right === '0px') {
				closeMobileMenu();
			} else {
				openMobileMenu();
			}
			return;
		}

		var closeBtn = e.target.closest('.close-this-menu, .sidr-class-close-this-menu, #close-menu');
		if (closeBtn) {
			e.preventDefault();
			closeMobileMenu();
			return;
		}

		if (sidr && sidr.style.display === 'block' && sidr.style.right === '0px') {
			if (!e.target.closest('#sidr-right') && !e.target.closest('#responsive-menu-button')) {
				closeMobileMenu();
			}
		}

		// 1. Gallery Left / Right buttons
		var leftBtn = e.target.closest('#gallery-image-list .left');
		if (leftBtn) {
			e.preventDefault();
			var items = document.querySelector('#images .items');
			if (items) {
				items.style.transition = 'left 0.1s ease-in-out';
				var currentLeft = parseInt(window.getComputedStyle(items).left, 10) || 0;
				var off = currentLeft + 76;
				items.style.left = (off < 0 ? off : 0) + 'px';
			}
			return;
		}

		var rightBtn = e.target.closest('#gallery-image-list .right');
		if (rightBtn) {
			e.preventDefault();
			var itemsR = document.querySelector('#images .items');
			if (itemsR) {
				itemsR.style.transition = 'left 0.1s ease-in-out';
				var tot = itemsR.querySelectorAll('a').length;
				var currentLeftR = parseInt(window.getComputedStyle(itemsR).left, 10) || 0;
				var offR = currentLeftR - 76;
				if (-(tot - 5) * 76 < offR) {
					itemsR.style.left = offR + 'px';
				}
			}
			return;
		}

		// 2. AJAX Tabs in Sidebars
		var tabLink = e.target.closest('.tabs li a.ajax, .tabnav li a.ajax, a.ajax');
		if (tabLink) {
			var tabsList = tabLink.closest('.tabs, .tabnav');
			if (tabsList) {
				e.preventDefault();
				var url = tabLink.getAttribute('href');
				if (url && url !== '#') {
					// Handle remember cookies
					var tabsConfig = {
						'last-sidebar-tab': { tab1: 'pages', tab3: 'gallery' },
						'last-facts-tab': { tab1: 'fact-all', tab2: 'fact-rs' },
						'last-mbs-tab': { tab1: 'all', tab2: 'friends', tab3: 'music' },
						'last-rsnews-tab': { tab1: 'runescape', tab2: 'oldschool' }
					};

					for (var pos in tabsConfig) {
						var values = tabsConfig[pos];
						var i = 0;
						for (var k in values) {
							var tabName = values[k];
							if (tabLink.classList.contains('remember-' + tabName)) {
								if (i === 0) {
									document.cookie = pos + '=; path=/; max-age=0;';
								} else {
									document.cookie = pos + '=' + tabName + '; path=/; max-age=' + (7 * 86400) + ';';
								}
							}
							i++;
						}
					}

					// Find ajaxbox container
					var container = tabLink.closest('.box, .widget, .tabwidget, .tab-container');
					var ajaxbox = container ? container.querySelector('.ajaxbox') : null;
					if (!ajaxbox && tabLink.parentElement && tabLink.parentElement.parentElement) {
						ajaxbox = tabLink.parentElement.parentElement.nextElementSibling;
						while (ajaxbox && !ajaxbox.classList.contains('ajaxbox')) {
							ajaxbox = ajaxbox.nextElementSibling;
						}
					}

					if (ajaxbox) {
						ajaxbox.style.opacity = '0.6';
						ajaxbox.style.transition = 'opacity 0.25s';

						fetch(url)
							.then(function (res) { return res.text(); })
							.then(function (html) {
								ajaxbox.innerHTML = html;
								ajaxbox.style.opacity = '1';
							})
							.catch(function (err) {
								ajaxbox.style.opacity = '1';
								console.error(err);
							});
					}

					// Toggle active class
					var tabLinks = tabsList.querySelectorAll('a');
					for (var j = 0; j < tabLinks.length; j++) {
						tabLinks[j].classList.remove('active');
					}
					tabLink.classList.add('active');
				}
				return;
			}
		}

		// 3. Sidebar Pagination & AJAX Pagination
		var pagerLink = e.target.closest('.ajax-pager a, a.ajax-module, .ajaxbox .pager-prev, .ajaxbox .pager-next, .ajaxbox .page-numbers');
		if (pagerLink) {
			// Skip tab links if caught by fallback
			if (pagerLink.classList.contains('ajax') && pagerLink.closest('.tabs, .tabnav')) {
				return;
			}
			e.preventDefault();
			var pUrl = pagerLink.getAttribute('href');
			if (pUrl && pUrl !== '#') {
				var pContainer = pagerLink.closest('.ajaxbox');
				if (!pContainer && pagerLink.parentElement && pagerLink.parentElement.parentElement) {
					pContainer = pagerLink.parentElement.parentElement;
				}

				if (pContainer) {
					pContainer.style.opacity = '0.5';
					pContainer.style.transition = 'opacity 0.25s';

					fetch(pUrl)
						.then(function (res) { return res.text(); })
						.then(function (html) {
							pContainer.innerHTML = html;
							pContainer.style.opacity = '1';
						})
						.catch(function (err) {
							pContainer.style.opacity = '1';
							console.error(err);
						});
				}
			}
			return;
		}
	});
});
