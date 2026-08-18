<style>
.exs-tooltip {
	position: fixed;
	display: none;
	background: rgba(20, 20, 20, 0.95);
	color: #fff;
	padding: 5px 9px;
	font-size: 11px;
	line-height: 1.3;
	border-radius: 4px;
	z-index: 10000;
	pointer-events: none;
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
	white-space: nowrap;
	border: 1px solid rgba(255, 255, 255, 0.15);
	font-family: sans-serif;
}
</style>
<script>
(function() {
	function initTooltip() {
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
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initTooltip);
	} else {
		initTooltip();
	}
})();
</script>
