(function($) {
"use strict";

 			//Shortcodes
           tinymce.PluginManager.add( 'vibeShortcodes', function( editor, url ) {

				editor.addCommand("vibePopup", function ( a, params ){
					var popup = params.identifier;
					tb_show("Insert Shortcode", url + "/popup.php?popup=" + popup + "&width=" + 800);
				});
     
                editor.addButton( 'vibe_button', {
                    type: 'splitbutton',
                    icon: 'icon vibe-icon',
					title:  'Vibe Shortcodes',
					onclick : function(e) {},
					menu: [
					{text: vibe_shortcode_icon_strings.accordion,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.accordion,identifier: 'accordion'})
					}},
					{text: vibe_shortcode_icon_strings.buttons,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.buttons,identifier: 'button'})
					}},
					{text: vibe_shortcode_icon_strings.columns,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.columns,identifier: 'columns'})
					}},
					{text: vibe_shortcode_icon_strings.counter,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.counter,identifier: 'counter'})
					}},
					{text: vibe_shortcode_icon_strings.course,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.course,identifier: 'course'})
					}},
					{text: vibe_shortcode_icon_strings.divider,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.divider,identifier: 'divider'})
					}},
					{text: vibe_shortcode_icon_strings.forms,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.forms,identifier: 'forms'})
					}},
					{text: vibe_shortcode_icon_strings.gallery,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.gallery,identifier: 'gallery'})
					}},
					{text: vibe_shortcode_icon_strings.google_maps,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.google_maps,identifier: 'maps'})
					}},
					{text: vibe_shortcode_icon_strings.heading,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.heading,identifier: 'heading'})
					}},
					{text: vibe_shortcode_icon_strings.icons,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.icons,identifier: 'icons'})
					}},
					{text: vibe_shortcode_icon_strings.iframe,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.iframe,identifier: 'iframe'})
					}},
					{text: vibe_shortcode_icon_strings.note,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.note,identifier: 'note'})
					}},
					{text: vibe_shortcode_icon_strings.popups,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.popups,identifier: 'popups'})
					}},
					{text: vibe_shortcode_icon_strings.progress_bar,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.progress_bar,identifier: 'progressbar'})
					}},
					{text: vibe_shortcode_icon_strings.pull_quote,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.pull_quote,identifier: 'pullquote'})
					}},
					{text: vibe_shortcode_icon_strings.round_progress,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.round_progress,identifier: 'roundprogress'})
					}},
					{text: vibe_shortcode_icon_strings.survey_result,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.survey_result,identifier: 'survey_result'})
					}},
					{text: vibe_shortcode_icon_strings.tabs,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.tabs,identifier: 'tabs'})
					}},
					{text: vibe_shortcode_icon_strings.team,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.team,identifier: 'team_member'})
					}},
					{text: vibe_shortcode_icon_strings.testimonial,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.testimonial,identifier: 'testimonial'})
					}},
					{text: vibe_shortcode_icon_strings.tooltips,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.tooltips,identifier: 'tooltip'})
					}},
					{text: vibe_shortcode_icon_strings.video,onclick:function(){
						editor.execCommand("vibePopup", false, {title: vibe_shortcode_icon_strings.video,identifier: 'iframevideo'})
					}},
					]                
        	  });
         
          });  
 
})(jQuery);
