/**
 * Lazy-load script for anything with a 'native-lazyload-js-fallback' class.
 *
 * @link https://developers.google.com/web/fundamentals/performance/lazy-loading-guidance/images-and-video/
 *
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
( function() {
	let lazyElements = [].slice.call( document.querySelectorAll( '.native-lazyload-js-fallback' ) );

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
					delete lazyElement.dataset.src;
					if ( lazyElement.dataset.srcset ) {
						lazyElement.srcset = lazyElement.dataset.srcset;
						delete lazyElement.dataset.srcset;
					}
					if ( lazyElement.dataset.sizes ) {
						lazyElement.sizes = lazyElement.dataset.sizes;
						delete lazyElement.dataset.sizes;
					}
					lazyElement.classList.remove( 'native-lazyload-js-fallback' );
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
								delete lazyElement.dataset.src;
								if ( lazyElement.dataset.srcset ) {
									lazyElement.srcset = lazyElement.dataset.srcset;
									delete lazyElement.dataset.srcset;
								}
								if ( lazyElement.dataset.sizes ) {
									lazyElement.sizes = lazyElement.dataset.sizes;
									delete lazyElement.dataset.sizes;
								}
								lazyElement.classList.remove( 'native-lazyload-js-fallback' );
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
