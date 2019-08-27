/**
 * Lazy-load images script.
 *
 * @link https://developers.google.com/web/fundamentals/performance/lazy-loading-guidance/images-and-video/
 */
( function() {
	let lazyElements = [].slice.call( document.querySelectorAll( '.lazy' ) );

	if ( 'IntersectionObserver' in window ) {
		const lazyObserver = new IntersectionObserver( function( entries ) {
			entries.forEach( function( entry ) {
				if ( entry.isIntersecting ) {
					const lazyElement = entry.target;
					if ( ! lazyElement.dataset.src ) {
						lazyObserver.unobserve( lazyElement );
						return;
					}
					lazyElement.src = lazyElement.dataset.src;
					if ( lazyElement.dataset.srcset ) {
						lazyElement.srcset = lazyElement.dataset.srcset;
					}
					if ( lazyElement.dataset.sizes ) {
						lazyElement.sizes = lazyElement.dataset.sizes;
					}
					lazyObserver.unobserve( lazyElement );
				}
			} );
		} );

		lazyElements.forEach( function( lazyElement ) {
			lazyObserver.observe( lazyElement );
		} );
	} else {
		// For older browsers lacking IntersectionObserver support.
		let active = false;

		const lazyLoad = function() {
			if ( false === active ) {
				active = true;

				setTimeout( function() {
					lazyElements.forEach( function( lazyElement ) {
						if ( ( lazyElement.getBoundingClientRect().top <= window.innerHeight && 0 <= lazyElement.getBoundingClientRect().bottom ) && 'none' !== getComputedStyle( lazyElement ).display ) {
							if ( lazyElement.dataset.src ) {
								lazyElement.src = lazyElement.dataset.src;
								if ( lazyElement.dataset.srcset ) {
									lazyElement.srcset = lazyElement.dataset.srcset;
								}
								if ( lazyElement.dataset.sizes ) {
									lazyElement.sizes = lazyElement.dataset.sizes;
								}
							}

							lazyElements = lazyElements.filter( function( element ) {
								return element !== lazyElement;
							} );

							if ( 0 === lazyElements.length ) {
								document.removeEventListener( 'scroll', lazyLoad );
								window.removeEventListener( 'resize', lazyLoad );
								window.removeEventListener( 'orientationchange', lazyLoad );
							}
						}
					} );

					active = false;
				}, 200 );
			}
		};

		document.addEventListener( 'scroll', lazyLoad );
		window.addEventListener( 'resize', lazyLoad );
		window.addEventListener( 'orientationchange', lazyLoad );
	}
}() );
