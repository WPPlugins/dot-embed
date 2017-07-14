/*
Plugin Name: Dot Embed
Plugin URI: http://dot.vu
Description: Embed Dot pages and apps using this code: <code>[dotembed url="" title="" dotext="" dotid="" pageext="" pageid="" width="" height="" ratio="" loading="" loadingcolor=""]</code>
Version: 2.1.4
Author: Pedro Figueiredo
Author URI: http://dot.vu
*/
$ = $ || jQuery;
(function(global){

  if (!Date.now) {
    Date.now = function now() {
      return new Date().getTime();
    };
  }

  /*******************************************************************
   *
   * Iframe loading
   *
   *******************************************************************/
  var loadIframe = function() {
    var __$ = $ || jQuery;
    var $dotembed = __$(this);
    // loading div to hide on load
    var $loading = $dotembed.find('.dotembed-loading');
    // iframe container    
    var $ifc = $dotembed.find('.dotembed-iframe-container');
    // get IDs
    var dotId = $ifc.data('dot-id');
    var pageId = $ifc.data('page-id');
    if(!dotId || !pageId) return;
    // get other data
    var height = $dotembed.data('dot-height');
    var width = $dotembed.data('dot-width');
    var ratio = $dotembed.data('dot-ratio');
    var dynamicHeight = ( height === "auto" && !ratio );
    // if a ratio is set, height is ignored
    if(ratio) height = "auto";
    // get extensions
    var dotExt = $ifc.data('dot-ext') || ('_' + dotId);
    var pageExt = $ifc.data('page-ext') || ('_' + pageId);
    // build iframe
    var $iframe = __$("<iframe width='" + width + "' height='" + height + "' frameborder='0'>");
    // set no scrolling in iframe if dynamicHeight
    if(dynamicHeight) $iframe.attr('scrolling','auto');
    // set on load finished
    global.dotPM.onMessage(dotId, pageId, 'load_finished', function(){
      $loading.toggle(false);
      $ifc.toggle(true);
      $ifc.addClass('active');
      
      if(dynamicHeight) {
        global.dotPM.onMessage(dotId, pageId, 'height_update', function(data) {
          data.hasOwnProperty('height')
            && $ifc.height(data.height)
            && $iframe.height(data.height);
        });

        var lastRequest = 0;
        var requestResize = function(){
          var newRequest = Date.now();
          if(lastRequest + 2000 < newRequest) {
            global.dotPM.sendMessage(dotId, pageId, 'request_height', '*', {}, $iframe[0].contentWindow);
            lastRequest = newRequest;
          }
        };
        window.addEventListener("resize", requestResize);
        requestResize();
        setTimeout(requestResize, 1000);
        setTimeout(requestResize, 2000);
        setTimeout(requestResize, 3000);
        //global.ahem = requestResize;
      }
    });
    // init loading state
    $ifc.toggle(false);
    // load iframe to DOM
    $ifc.empty();
    $ifc.append($iframe);
    $iframe.attr('src','https://dot.vu/p/' + dotExt +'/' + pageExt);
  }

  $(document).ready(function(){
    var __$;
    try {
      __$ = $ || jQuery;
      __$('.dotembed').each(loadIframe);
    } catch(e) { console.log("Dot embed: issue loading jquery."); }
  });

})(window);