$(document).ready(function() {
	
	(function($){
	    $.gourd = ( function(my) {
	        my.ui = {
	            addmsg: function(msg,type) {
	                var msgblock = '<div class="content ' + type + ' hidden" data-role="systemmessage"><div class="messageBox"><p><strong>' + type + '!</strong> ' + msg + '</p></p><div class="clearer"></div></div><div class="closeBox"><p><a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button" data-role="closemessage"><span class="ui-icon ui-icon-closethick">close</span></a></p><div class="clearer"></div></div><div class="clearer"></div></div>';
	                $("a.ui-dialog-titlebar-close").live( 'click', function() {
	                    $(this).closest("div.content").slideUp("slow", function() {
	                        $(this).remove();
	                    });
	                    return false;
	                });
	                $("#msgs").append(msgblock);
	                $("#msgs > div.content").last().slideDown().delay(5000).slideUp();
	                return my;
	            },
	            alert: function(msg,domnode) {
	            	if (my.ui.getTheme()) {
		                var id;
		                if (!domnode || !$(domnode)) {
		                    id = my.uniqid();
		                    domnode = $('<div id="'+id+'" title="alert"><p>'+msg+'</p></div>');
		                } else {
		                    id = domnode.attr('id');   
		                }
		                $(domnode).dialog({
		                    modal: true,
		                    buttons: {
		                        Ok: function() {
		                        $( this ).dialog( "close" );
		                        }
		                    }
		                });
		                $(domnode).dialog('open');
	            	} else {
	            		alert(msg);
	            	}
	            },
	            getTheme: function() {
	                var sheets = document.styleSheets,
	                	r;
	                r = false;
	                for (i in sheets) {
	                    if (/jquery-ui.css$/.test(sheets[i].href) || /\/themes\//.test(sheets[i].href) ) {
	                        r = sheets[i];
	                        break;
	                    }        
	                }            
	                return r;
	            },
	            setTheme: function(f) {
	                var currenttheme = my.ui.getTheme();
	                if (currenttheme) {
	                	delete currenttheme;
					}
					$('<link rel="stylesheet" href="'+f+'" />').appendTo("head");
	            },
	            deleteTheme: function() {
	                var currenttheme = my.ui.getTheme();
	                if (currenttheme) {
	                	delete currenttheme;
					}            
	            }
	        };
	
	        
	        my.uniqid = function() {
	            var randomLetters = function(n) {
	                var r = '',
	                    thisletter;
	                for(j=0; j<=n; j++) {
	                    thisletter = Math.ceil ( (Math.random() * 6) + 9 );
	                    r = r + thisletter.toString(16);
	                }
	                return r;
	            },
	                randomAlphaNum = function(n) {
	                var r = '',
	                    thischar;
	                for(j=0; j<=n; j++) {
	                    thischar = Math.ceil ( Math.random() * 15 );
	                    r = r + thischar.toString(16);
	                }
	                return r;
	            },
	                z     = new Date().getTime(),    
	                r    = randomLetters(3) + z.toString(16) + randomAlphaNum(16);
	            return r.toUpperCase();
	        };
	        
	        my.randomColour = function(shade) {
	            var red     = Math.floor(Math.random()*85.3333),
	                blue    = Math.floor(Math.random()*85.3333),
	                green    = Math.floor(Math.random()*85.3333),
	                pad     = "666666",
	                r        = "#666666";
	                
	            switch(shade) {
	            
	                case 'light':
	                red        = red + 171;
	                blue    = blue + 171;
	                green    = green + 171;
	                pad     = "FFFFFF";
	                break;
	                
	                case 'medium':
	                red     = red + 85;
	                blue     = blue + 85;
	                green     = green + 85;
	                pad     = "999999"
	                break;
	                
	                default:
	                //    num is fine
	                pad     = '000000';
	            }
	            r = '#'
	            + ( pad + red.toString(16)  ).substr(-2)
	            + ( pad + green.toString(16)).substr(-2)
	            + ( pad + blue.toString(16) ).substr(-2);
	            return r;
	        };
	        my.dbg = function() {
	            console.log(this);
	            console.log(my);
	            return my;
	        };
	        return my;
	    })({});
	})(jQuery);

});
