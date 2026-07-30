( function () {
	'use strict';

	var pendingFrame = 0;

	function isViewportLayout( wrapper ) {
		return wrapper.classList.contains( 'pixels-mega-menu-layout-full_width' ) ||
			wrapper.classList.contains( 'pixels-mega-menu-layout-container' );
	}

	function getViewportTop( wrapper, wrapperRect ) {
		var parentItem = wrapper.closest( '.pixels-mega-menu-item' );
		var parentSubMenu = wrapper.closest( '.sub-menu' );

		if ( wrapper.classList.contains( 'pixels-mega-menu-panel' ) && parentItem ) {
			return parentItem.getBoundingClientRect().bottom;
		}

		if ( wrapperRect.top > 0 ) {
			return wrapperRect.top;
		}

		if ( parentSubMenu ) {
			var subMenuRect = parentSubMenu.getBoundingClientRect();

			if ( subMenuRect.top > 0 ) {
				return subMenuRect.top;
			}
		}

		return parentItem ? parentItem.getBoundingClientRect().bottom : 0;
	}

	function positionWrapper( wrapper ) {
		if ( ! wrapper ) {
			return;
		}

		wrapper.classList.remove( 'pixels-mega-menu-viewport-positioned' );
		wrapper.classList.add( 'pixels-mega-menu-measuring' );
		wrapper.style.setProperty( '--pixels-mega-menu-left', '0px' );
		wrapper.style.removeProperty( '--pixels-mega-menu-viewport-left' );
		wrapper.style.removeProperty( '--pixels-mega-menu-top' );

		var wrapperRect = wrapper.getBoundingClientRect();

		if ( ! wrapperRect.width ) {
			wrapper.classList.remove( 'pixels-mega-menu-measuring' );
			return;
		}

		var width = Math.min( wrapperRect.width, window.innerWidth );

		if ( isViewportLayout( wrapper ) ) {
			var viewportLeft = wrapper.classList.contains( 'pixels-mega-menu-layout-full_width' ) ? 0 : ( window.innerWidth - width ) / 2;

			wrapper.classList.add( 'pixels-mega-menu-viewport-positioned' );
			wrapper.style.setProperty( '--pixels-mega-menu-viewport-left', viewportLeft + 'px' );
			wrapper.style.setProperty( '--pixels-mega-menu-top', getViewportTop( wrapper, wrapperRect ) + 'px' );
		} else {
			wrapper.style.setProperty( '--pixels-mega-menu-left', '0px' );
		}

		wrapper.classList.remove( 'pixels-mega-menu-measuring' );
	}

	function positionMegaMenus() {
		document.querySelectorAll( '.pixels-mega-menu-panel, .pixels-mega-menu-content' ).forEach( positionWrapper );
	}

	function schedulePositioning() {
		if ( pendingFrame ) {
			window.cancelAnimationFrame( pendingFrame );
		}

		pendingFrame = window.requestAnimationFrame( function () {
			pendingFrame = 0;
			positionMegaMenus();
		} );

		window.setTimeout( positionMegaMenus, 60 );
		window.setTimeout( positionMegaMenus, 180 );
	}

	document.addEventListener( 'mouseover', schedulePositioning );
	document.addEventListener( 'focusin', schedulePositioning );
	window.addEventListener( 'resize', schedulePositioning );
	window.addEventListener( 'load', schedulePositioning );
	document.addEventListener( 'DOMContentLoaded', schedulePositioning );
}() );
