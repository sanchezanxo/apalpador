/**
 * Apalpador Frontend Scripts
 *
 * @package Apalpador
 */

(function() {
	'use strict';

	// Check for reduced motion preference.
	var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	// Options passed from PHP.
	var options = window.apalpadorOptions || {};

	/**
	 * Initialize when DOM is ready.
	 */
	document.addEventListener('DOMContentLoaded', function() {
		initEntryAnimation();
		initClickAnimation();
		initBubble();
		initSnowEffect();
		initShootingStarEffect();
	});

	/**
	 * Handle entry animation completion.
	 * Remove entry classes after animation ends to prevent conflicts with click animations.
	 */
	function initEntryAnimation() {
		var apalpador = document.getElementById('apalpador');
		if (!apalpador || prefersReducedMotion) {
			return;
		}

		// Listen for animation end.
		apalpador.addEventListener('animationend', function handler(e) {
			// Only handle entry animations.
			if (e.animationName.indexOf('apalpador-slide') === 0 ||
				e.animationName.indexOf('apalpador-fade') === 0 ||
				e.animationName.indexOf('apalpador-bounce-in') === 0 ||
				e.animationName.indexOf('apalpador-rotate-in') === 0) {

				// Remove entry animation classes.
				apalpador.classList.remove(
					'apalpador-entry-slide',
					'apalpador-entry-fade',
					'apalpador-entry-bounce',
					'apalpador-entry-rotate'
				);

				// Remove this listener - entry animation only happens once.
				apalpador.removeEventListener('animationend', handler);
			}
		});
	}

	/**
	 * Initialize click animation on the Apalpador.
	 */
	function initClickAnimation() {
		var apalpador = document.getElementById('apalpador');
		if (!apalpador) {
			return;
		}

		var clickAnimation = apalpador.getAttribute('data-click-animation');
		if (!clickAnimation || clickAnimation === 'none' || prefersReducedMotion) {
			return;
		}

		var isAnimating = false;

		apalpador.addEventListener('click', function() {
			// Prevent multiple clicks during animation.
			if (isAnimating) {
				return;
			}

			isAnimating = true;

			// Add the click animation class.
			apalpador.classList.add('apalpador-click-' + clickAnimation);

			// Remove the class after animation completes.
			setTimeout(function() {
				apalpador.classList.remove('apalpador-click-' + clickAnimation);
				isAnimating = false;
			}, 600);
		});
	}

	/**
	 * Initialize speech bubble functionality.
	 */
	function initBubble() {
		if (!options.bubbleEnabled) {
			return;
		}

		var apalpador = document.getElementById('apalpador');
		if (!apalpador) {
			return;
		}

		var bubble = apalpador.querySelector('.apalpador-bubble');
		if (!bubble) {
			return;
		}

		var trigger = options.bubbleTrigger || 'once';
		var bubbleTimeout = null;
		var isVisible = false;

		/**
		 * Show the bubble with animation.
		 */
		function showBubble() {
			if (isVisible) {
				return;
			}

			isVisible = true;
			bubble.classList.remove('animate-out');
			bubble.classList.add('visible', 'animate-in');

			// Auto-hide after 4 seconds.
			bubbleTimeout = setTimeout(function() {
				hideBubble();
			}, 4000);
		}

		/**
		 * Hide the bubble with animation.
		 */
		function hideBubble() {
			if (!isVisible) {
				return;
			}

			bubble.classList.remove('animate-in');
			bubble.classList.add('animate-out');

			// Remove visible class after animation.
			setTimeout(function() {
				bubble.classList.remove('visible', 'animate-out');
				isVisible = false;
			}, 300);

			if (bubbleTimeout) {
				clearTimeout(bubbleTimeout);
				bubbleTimeout = null;
			}
		}

		if (trigger === 'once') {
			// Show bubble once after entry animation completes.
			// Wait a bit after page load for entry animation.
			setTimeout(function() {
				showBubble();
			}, 1500);
		} else if (trigger === 'click') {
			// Toggle bubble on click.
			apalpador.addEventListener('click', function() {
				if (isVisible) {
					hideBubble();
				} else {
					showBubble();
				}
			});
		} else if (trigger === 'hover') {
			// Show bubble on hover.
			apalpador.addEventListener('mouseenter', function() {
				showBubble();
			});

			apalpador.addEventListener('mouseleave', function() {
				hideBubble();
			});
		}
	}

	/**
	 * Initialize snow effect.
	 */
	function initSnowEffect() {
		if (!options.snowEnabled || prefersReducedMotion) {
			return;
		}

		// Get snowflake count based on density.
		var densityMap = {
			low: 25,
			medium: 50,
			high: 100
		};
		var snowflakeCount = densityMap[options.snowDensity] || 50;

		// Create container.
		var container = document.createElement('div');
		container.className = 'apalpador-snow-container';
		container.setAttribute('aria-hidden', 'true');
		document.body.appendChild(container);

		// Snowflake pool for recycling.
		var snowflakes = [];
		var screenWidth = window.innerWidth;
		var screenHeight = window.innerHeight;

		// Update screen dimensions on resize.
		window.addEventListener('resize', function() {
			screenWidth = window.innerWidth;
			screenHeight = window.innerHeight;
		});

		/**
		 * Create a snowflake with random properties.
		 */
		function createSnowflake() {
			var flake = document.createElement('div');
			flake.className = 'apalpador-snowflake';
			container.appendChild(flake);

			return {
				element: flake,
				x: 0,
				y: 0,
				size: 0,
				speed: 0,
				wind: 0,
				wobble: 0,
				wobbleSpeed: 0
			};
		}

		/**
		 * Reset snowflake to start position with new random values.
		 */
		function resetSnowflake(flake) {
			flake.x = Math.random() * screenWidth;
			flake.y = -10;
			flake.size = Math.random() * 4 + 2; // 2-6px
			flake.speed = Math.random() * 1 + 0.5; // 0.5-1.5 pixels per frame
			flake.wind = Math.random() * 0.5 - 0.25; // -0.25 to 0.25
			flake.wobble = 0;
			flake.wobbleSpeed = Math.random() * 0.02 + 0.01;

			flake.element.style.width = flake.size + 'px';
			flake.element.style.height = flake.size + 'px';
			flake.element.style.opacity = Math.random() * 0.4 + 0.4; // 0.4-0.8
		}

		/**
		 * Update snowflake position.
		 */
		function updateSnowflake(flake) {
			// Move down.
			flake.y += flake.speed;

			// Wobble horizontally.
			flake.wobble += flake.wobbleSpeed;
			flake.x += flake.wind + Math.sin(flake.wobble) * 0.5;

			// Reset if out of screen.
			if (flake.y > screenHeight + 10 || flake.x < -10 || flake.x > screenWidth + 10) {
				resetSnowflake(flake);
			}

			// Apply position.
			flake.element.style.transform = 'translate(' + flake.x + 'px, ' + flake.y + 'px)';
		}

		// Initialize snowflakes with staggered positions.
		for (var i = 0; i < snowflakeCount; i++) {
			var flake = createSnowflake();
			resetSnowflake(flake);
			// Distribute initial Y positions across screen.
			flake.y = Math.random() * screenHeight;
			snowflakes.push(flake);
		}

		/**
		 * Animation loop using requestAnimationFrame.
		 */
		function animate() {
			for (var i = 0; i < snowflakes.length; i++) {
				updateSnowflake(snowflakes[i]);
			}
			requestAnimationFrame(animate);
		}

		// Start animation.
		requestAnimationFrame(animate);
	}

	/**
	 * Initialize shooting star effect.
	 */
	function initShootingStarEffect() {
		if (!options.starEnabled || prefersReducedMotion) {
			return;
		}

		var frequency = (options.starFrequency || 10) * 1000; // Convert to milliseconds.
		var star = null;

		/**
		 * Create the shooting star element.
		 */
		function createStar() {
			star = document.createElement('div');
			star.className = 'apalpador-shooting-star';
			star.setAttribute('aria-hidden', 'true');
			document.body.appendChild(star);
		}

		/**
		 * Trigger a shooting star animation.
		 */
		function shootStar() {
			if (!star) {
				createStar();
			}

			var screenWidth = window.innerWidth;
			var isMobile = screenWidth < 768;
			var isTablet = screenWidth >= 768 && screenWidth < 1024;

			// Start from left side of screen, random Y in top 20%.
			var startX = Math.random() * (screenWidth * 0.2); // Left 20% of screen.
			var startY = Math.random() * (window.innerHeight * 0.15) + 15; // Top 15%, min 15px from top.

			// Distance and duration based on screen size.
			var distance, duration, drop;

			if (isMobile) {
				// Mobile: cross good portion of screen.
				distance = screenWidth * (0.5 + Math.random() * 0.3); // 50-80% of screen.
				duration = 1 + Math.random() * 0.3; // 1-1.3s
				drop = Math.random() * 20 + 10; // 10-30px drop.
			} else if (isTablet) {
				// Tablet: medium values.
				distance = screenWidth * (0.5 + Math.random() * 0.2); // 50-70% of screen.
				duration = 1.3 + Math.random() * 0.4; // 1.3-1.7s
				drop = Math.random() * 25 + 20; // 20-45px drop.
			} else {
				// Desktop: longer distance, can be faster.
				distance = screenWidth * (0.4 + Math.random() * 0.25); // 40-65% of screen.
				duration = 1.5 + Math.random() * 0.5; // 1.5-2s
				drop = Math.random() * 30 + 25; // 25-55px drop.
			}

			// Set CSS custom properties for the animation.
			star.style.setProperty('--star-distance', distance + 'px');
			star.style.setProperty('--star-drop', drop + 'px');
			star.style.setProperty('--star-duration', duration + 's');

			// Position the star.
			star.style.left = startX + 'px';
			star.style.top = startY + 'px';

			// Remove active class, force reflow, add active class.
			star.classList.remove('active');
			void star.offsetWidth;
			star.classList.add('active');

			// Schedule next star.
			scheduleNextStar();
		}

		/**
		 * Schedule the next shooting star with some randomness.
		 */
		function scheduleNextStar() {
			// Add some randomness: 50% to 150% of base frequency.
			var nextTime = frequency * (0.5 + Math.random());
			setTimeout(shootStar, nextTime);
		}

		// Start the first star after a short delay.
		setTimeout(shootStar, 2000);
	}

})();
