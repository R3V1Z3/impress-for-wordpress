var replace = document.getElementById('impress-replace').innerHTML;

var iFrame = document.querySelector('#impress-iframe').contentWindow.document;
var content = '<html><head></head>';
content += '<body class="impress-not-supported">';
content += replace;
content += '<script src="//netdna.impressjscdn.com/impressjs/0.5.3/js/impress.js"></script>';
content += '<script>impress().init();</script>';
content += '</body>';
content += '</html>';

// add parent styles to iframe
var iFrameHead = iFrame.getElementsByTagName("head")[0];
var parentStyles = parent.document.getElementsByTagName("link");
for (var i = 0; i < parentStyles.length; i++) {
    iFrameHead.appendChild(parentStyles[i].cloneNode(true));
}

// clear <div id="impress"> content in iframe parent
var div = document.getElementById('impress-replace');
while(div.firstChild){
    div.removeChild(div.firstChild);
}

iFrame.open('text/html', 'replace');
iFrame.write(content);
iFrame.close();