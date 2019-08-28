/**
 * Lazy-load script for anything with a 'lazy' class.
 *
 * @link https://developers.google.com/web/fundamentals/performance/lazy-loading-guidance/images-and-video/
 *
 * Native Lazyload, Copyright 2019 Google LLC
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
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
