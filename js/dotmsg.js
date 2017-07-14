/*
Plugin Name: Dot Embed
Plugin URI: http://dot.vu
Description: Embed Dot pages and apps using this code: <code>[dotembed url="" title="" dotext="" dotid="" pageext="" pageid="" width="" height="" ratio="" loading="" loadingcolor=""]</code>
Version: 2.1.4
Author: Emanuel Silva
Author URI: http://dot.vu
*/
$ = $ || jQuery;
(function(global){
  var $ = $ || jQuery;
  
  /*************************************************************************
  *
  * Underline methods
  *
  /************************************************************************/
  var onMessage = function(cb){
    $(window).on("message onmessage", function(e) {
      cb(e.originalEvent.data);
    });
  };

  var sendMessage = function(target, targetOrigin, data){
    target.postMessage(data, targetOrigin);
  };

  /*************************************************************************
  *
  * Dot Post Messages
  *
  /************************************************************************/
  var getMessageData = function(dotId, pageId, event, data){
    try {
        // Needs to be a JSON
        var dotData = JSON.parse(data);

        return dotData
         && dotData.hasOwnProperty('dotId') && dotData.dotId == dotId
         && dotData.hasOwnProperty('pageId') && dotData.pageId == pageId
         && dotData.hasOwnProperty('event') && dotData.event == event
         && dotData;

    } catch(err){ return false; }
  };

  var onDotMessage = function(dotId, pageId, event, cb){
    onMessage(function(data){
      var dotData = getMessageData(dotId, pageId, event, data);
      if(dotData) return cb(dotData.data);
    });
  };

  var sendDotMessage = function(dotId, pageId, event, targetOrigin, data, target){
    target = target || parent;
    if(target)
      sendMessage(target, targetOrigin || '*', JSON.stringify({
        dotId: dotId.toString(),
        pageId: pageId,
        event: event,
        data: data
      }));
  };

  /*************************************************************************
  *
  * EXPORT
  *
  /************************************************************************/
  global.dotPM = {
    onMessage: onDotMessage,
    sendMessage: sendDotMessage,
  }

})(window);