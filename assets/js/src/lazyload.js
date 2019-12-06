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
	function hyphencase( key ) {
		return key.replace( /([a-z][A-Z])/g, function( match ) {
			return match[0] + '-' + match[1].toLowerCase();
		} );
	}

	/**
	 * Gets the value for a data attribute.
	 *
	 * @param {DOMNode} elem DOM node.
	 * @param {string}  key  Data attribute in camel-case.
	 * @return Data attribute value, or undefined if not set.
	 */
	function getData( elem, key ) {
		if ( elem.dataset ) {
			return elem.dataset[ key ];
		}
		const val = elem.getAttribute( 'data-' + hyphencase( key ) );
		if ( val === null || val === "" ) {
			return undefined;
		}
		return val;
	}

	/**
	 * Deletes a data attribute.
	 *
	 * @param {DOMNode} elem DOM node.
	 * @param {string}  key  Data attribute in camel-case.
	 */
	function deleteData( elem, key ) {
		if ( elem.dataset ) {
			delete elem.dataset[ key ];
			return;
		}
		elem.removeAttribute( 'data-' + hyphencase( key ) );
	}

	let lazyElements = [].slice.call( document.querySelectorAll( '.native-lazyload-js-fallback' ) );

	if ( 'IntersectionObserver' in window ) {
		const lazyObserver = new IntersectionObserver( function( entries ) {
			entries.forEach( function( entry ) {
				if ( entry.isIntersecting ) {
					const lazyElement = entry.target;
					if ( ! getData( lazyElement, 'src' ) ) {
						lazyObserver.unobserve( lazyElement );
						return;
					}
					lazyElement.src = getData( lazyElement, 'src' );
					deleteData( lazyElement, 'src' );
					if ( getData( lazyElement, 'srcset' ) ) {
						lazyElement.srcset = getData( lazyElement, 'srcset' );
						deleteData( lazyElement, 'srcset' );
					}
					if ( getData( lazyElement, 'sizes' ) ) {
						lazyElement.sizes = getData( lazyElement, 'sizes' );
						deleteData( lazyElement, 'sizes' );
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
							if ( getData( lazyElement, 'src' ) ) {
								lazyElement.src = getData( lazyElement, 'src' );
								deleteData( lazyElement, 'src' );
								if ( getData( lazyElement, 'srcset' ) ) {
									lazyElement.srcset = getData( lazyElement, 'srcset' );
									deleteData( lazyElement, 'srcset' );
								}
								if ( getData( lazyElement, 'sizes' ) ) {
									lazyElement.sizes = getData( lazyElement, 'sizes' );
									deleteData( lazyElement, 'sizes' );
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

		lazyLoad();
		document.addEventListener( 'scroll', lazyLoad );
		window.addEventListener( 'resize', lazyLoad );
		window.addEventListener( 'orientationchange', lazyLoad );
	}
}() );
