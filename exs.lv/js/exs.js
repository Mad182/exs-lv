$(document).ready(function($) {
	$('#responsive-menu-button').sidr({
		name: 'sidr-right',
		speed: 50,
		side: 'right',
		source: '#swipe-menu-responsive'
	});

	$('a.sidr-class-close-this-menu').click(function() {
		$('div.sidr').css({
			'right': '-476px'
		});
		$('body').css({
			'right': '0'
		});
	});

	// Global Tooltip Handler for .cluetip elements
	var tip = document.querySelector('.exs-tooltip');
	if (!tip) {
		tip = document.createElement('div');
		tip.className = 'exs-tooltip';
		document.body.appendChild(tip);
	}

	document.addEventListener('mouseover', function(e) {
		var target = e.target.closest('.cluetip');
		if (!target) return;
		var title = target.getAttribute('title') || target.getAttribute('data-title');
		if (!title) return;
		if (target.hasAttribute('title')) {
			target.setAttribute('data-title', title);
			target.removeAttribute('title');
		}
		tip.textContent = title;
		tip.style.display = 'block';
	});

	document.addEventListener('mousemove', function(e) {
		if (tip.style.display === 'block') {
			var x = e.clientX + 10;
			var y = e.clientY + 15;
			if (x + tip.offsetWidth > window.innerWidth - 10) {
				x = e.clientX - tip.offsetWidth - 10;
			}
			if (y + tip.offsetHeight > window.innerHeight - 10) {
				y = e.clientY - tip.offsetHeight - 10;
			}
			tip.style.left = x + 'px';
			tip.style.top = y + 'px';
		}
	});

	document.addEventListener('mouseout', function(e) {
		var target = e.target.closest('.cluetip');
		if (target) {
			tip.style.display = 'none';
		}
	});
});

