var replace = document.getElementById('impress-replace').innerHTML;

var iFrame = document.querySelector('#impress-iframe').contentWindow.document;
var content = '<html style="overflow:hidden;"><head></head>';
content += '<body class="impress-not-supported">';
content += replace;
content += '<script src="//netdna.impressjscdn.com/impressjs/0.5.3/js/impress.js"></script>';
content += '<script>impress().init();</script>';
content += '</body>';
content += '</html>';

// clear <div id="impress"> content in iframe parent
var div = document.getElementById('impress-replace');
while(div.firstChild){
    div.removeChild(div.firstChild);
}

iFrame.open('text/html', 'replace');
iFrame.write(content);
iFrame.close();