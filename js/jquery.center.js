/* Plugin name: jQuery centerIt
*
* Author: Julien Knebel
* Version: 0.9
* Date: June 2011
*
* jQuery centerIt centers an element horizontally and/or vertically based on current window dimensions or based on a parent container dimensions.
* A very cool trick is that it also works on hidden elements or on a hidden element inside hidden parents.
*
* Sample codes *****************************************
* Center an element from the browser window: $('#someElement').centerIt();
* Center an element from a container: $('#someElement').centerIt({ parent: '#someContainer' });
* Center an element from a container and only horizontally: $('#someElement').centerIt({ parent: '#someContainer', vertical: false });
* Center an element from the browser window only vertically: $('#someElement').centerIt({ horizontal: false });
* Disable the autoCenter on browser window when being resized: $('#someElement').centerIt({ autoCenter: false });
*
*
* Copyright (c) 2011 Julien Knebel. For questions or support plz email me to contact[at]lebken[.]com
* Licensed under the MIT License.
*
* Browser support: ie6+, Firefox, Chrome, Safari, Opera
* jQuery version compatibility: 1.4 and above
*/
(function($){$.fn.centerIt=function(options){var defaults={horizontal:true,vertical:true,parent:false,autoCenter:true};var options=$.extend(defaults,options);return this.each(function(){var elem=$(this);var parent=(options.parent)?$(options.parent):false;function setCenter(){var totalWidth=(parent)?parent.width():$(window).width(),totalHeight=(parent)?parent.height():$(window).height(),hiddenElem=getHiddenDimensions(elem),elemWidth=(elem)?elem.outerWidth():hiddenElem.outerWidth,elemHeight=(elem)?elem.outerHeight():hiddenElem.outerHeight,centeredFromLeft=(options.horizontal)?(totalWidth/2)-(elemWidth/2):null,centeredFromTop=(options.vertical)?(totalHeight/2)-(elemHeight/2):null;function getHiddenDimensions(elem,includeMargin){var $item=elem,props={position:'absolute',visibility:'hidden',display:'block'},dim={width:0,height:0,innerWidth:0,innerHeight:0,outerWidth:0,outerHeight:0},$hiddenParents=$item.parents().andSelf().not(':visible'),includeMargin=(includeMargin==null)?false:includeMargin;var oldProps=[];$hiddenParents.each(function(){var old={};for(var name in props){old[name]=this.style[name];this.style[name]=props[name]}oldProps.push(old)});dim.width=$item.width();dim.outerWidth=$item.outerWidth(includeMargin);dim.innerWidth=$item.innerWidth();dim.height=$item.height();dim.innerHeight=$item.innerHeight();dim.outerHeight=$item.outerHeight(includeMargin);$hiddenParents.each(function(i){var old=oldProps[i];for(var name in props){this.style[name]=old[name]}});return dim}elem.css({position:'absolute',left:centeredFromLeft,top:centeredFromTop})}setCenter();if(options.autoCenter)$(window).bind('resize',setCenter)})}}(jQuery));


