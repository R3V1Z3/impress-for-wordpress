// source for impress.js, CDN only for now
var source = '//netdna.impressjscdn.com/impressjs/0.5.3/js/impress.js';

// get all impress-replace classes
var impresswp_items = document.getElementsByClassName("impresswp-replace");

// iterate over each replacement
for ( var i = 0; i < impresswp_items.length; i++ ) {
	var iframe_id = '#impresswp-iframe-' + ( i + 1 );
	var iframe = document.querySelector( iframe_id ).contentWindow.document;
	write_contents( iframe, impresswp_items[i].innerHTML, true, true );

	// hide contents, they're displayed anyway if js is disabled
	impresswp_items[ i ].style.display = "none";
}

function write_contents( document, html, include_impress, overflow) {
	var content = '<html';
	if ( overflow ) {
		content += ' style="overflow:hidden;"';
	}
	content += '><head>';
	//
	content += '</head>';
	content += '';
	content += '<body class="impress-not-supported">';
	content += html;
	if ( include_impress ) {
		content += '<script src="' + source + '"></script>';
		content += '<script>impress().init();</script>';
	}
	content += '</body>';
	content += '</html>';

	// write contents to iframe
	document.open( 'text/html', 'replace' );
	document.write( content );
	document.close();
}

// Fullscreen
function impress_fullscreen( iframe_id ) {
	var iframe = document.getElementById( iframe_id );
	if ( iframe.requestFullscreen ) {
		iframe.requestFullscreen();
	} else if ( iframe.msRequestFullscreen ) {
		iframe.msRequestFullscreen();
	} else if ( iframe.mozRequestFullScreen ) {
		iframe.mozRequestFullScreen();
	} else if ( iframe.webkitRequestFullscreen ) {
		iframe.webkitRequestFullscreen();
	}
	iframe.focus();
}

// Open iframe in a window and render raw html
function impress_printable( div_id ) {
	var div = document.getElementById( div_id );
	div.style.display = "block";
	var options = "menubar=1,resizable=1,scrollbars=1,status=1";
	var newWindow = window.open( '', '_blank', options );
	var win = newWindow.document;
	write_contents( win, div.innerHTML, false, false );
	div.style.display = "none";
}