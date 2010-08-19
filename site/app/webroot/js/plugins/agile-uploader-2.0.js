(function($) {
	var opts;
	var noURI;
	
	$.fn.agileUploaderSerializeFormData = function() {		
		if((typeof(opts.formId) == 'string') && ($('#'+opts.formId).length > 0)) {			
			return $('#'+opts.formId).serialize();		
		}
	}

	$.fn.agileUploaderPreview = function(image, fileName, fileExtension, fileSize, fileId) {
		var fileId = fileId; //fileName.replace(' ', '').replace('.', '');		
		if((typeof(image) != 'undefined') && (noURI !== true)) {			
			$('#id-'+fileId+' .agileUploaderFilePreview').html('<img src="'+image+'" />');
		} else {
			$('#id-'+fileId+' .agileUploaderFilePreview').html('<img src="'+opts.genericFileIcon+'" />');
		}
		$('#id-'+fileId+' .agileUploaderFileSize').text('('+fileSize+'Kb)');		
	}
	
	$.fn.agileUploaderSubmitted = function(data) {
		// If there's a div to put the return data into, do so
		if(typeof(opts.updateDiv) == 'string') {
			$(opts.updateDiv).empty();
			$(opts.updateDiv).append(data);
		}		
		// Re-direct or empty the list so another submission can be made
		if(typeof(opts.submitRedirect) == 'string') {
			window.location = opts.submitRedirect;
		} else {			
			$('#agileUploaderFileList').empty();
		}
	}
		
	$.fn.agileUploaderAttachFile = function(fileName, fileExtension, fileId) {
		// if in single file replace mode just empty the list visually, only the last attached file will be submitted by flash (rare, this shouldn't be w/ multiple uploads)
		if(opts.flashVars.file_limit == -1) { 			
			$('#agileUploaderFileList').empty();
		}
		$("#agileUploaderInfo").animate({ scrollTop: $("#agileUploaderInfo").attr("scrollHeight") }, opts.attachScrollSpeed);
		var alt = '';
		if ($('#agileUploaderFileList li').size() % 2 == 0) { alt = 'alt'; } 
		$('#agileUploaderFileList').append('<li id="id-'+fileId+'" class="'+alt+'"><div class="agileUploaderFilePreview" style="display: none;"></div><div class="agileUploaderFileName" style="display: none;">'+fileName+'</div><div id="agileUploaderCurrentProgress"></div><div class="agileUploaderFileSize" style="display: none;"></div><div class="agileUploaderRemoveFile" style="display:none;"><a href="#" id="remove-'+fileId+'" onClick="document.getElementById(\'agileUploaderSWF\').removeFile(\''+fileId+'\'); $(\'#id-'+fileId+'\').remove(); return false;"><img class="agileUploaderRemoveIcon" src="'+opts.removeIcon+'" alt="remove" /></a></div></li>');		
		// Check for IE, change css special for IE.
		if(/msie/i.test(navigator.userAgent) && !/opera/i.test(navigator.userAgent) === true) {
			$('#id-'+fileId).css('height', opts.flashVars.preview_max_height+5);
		} else {
			$('#id-'+fileId).css('height', opts.flashVars.preview_max_height);
		}		
		
		// If using a bar, the background gets the value of opts.progressBar, it can be '#123456' or 'url:("image.jpg")'  ... NOTE: no ending ;
		if((typeof(opts.progressBar) == 'string') && (opts.progressBar != 'percent')) {
		//console.info(opts.progressBar);
			$('#agileUploaderCurrentProgress').css('background', opts.progressBar);
		}
		
	}	
	
	$.fn.agileUploaderCurrentEncodeProgress = function(progress) {
		if(typeof(opts.progressBar) == 'string') {
			if(opts.progressBar == 'percent') {				
				$('#agileUploaderCurrentProgress').text(parseInt(progress)+'%');
			} else {				
				$('#agileUploaderCurrentProgress').css('width', parseInt(progress)+'%');				
			}			
		}
		if(progress >= 100) {
			$('#agileUploaderCurrentProgress').remove();
			$('.agileUploaderFileName, .agileUploaderRemoveFile, .agileUploaderFileSize, .agileUploaderFilePreview').show();
			// add remove all
			$('#agileUploaderRemoveAll').html('<a href="#" onClick="document.getElementById(\'agileUploaderSWF\').removeAllFiles(); $(\'#agileUploaderFileList\').empty(); $(\'#agileUploaderRemoveAll\').empty(); return false;">'+opts.removeAllText+'</a>');
		}
	}
	
	$.fn.agileUploaderMaxPostSize = function(lastFile, fileId) {		
		$('#id-'+fileId).remove(); // in case the row was visually added because it had a progress bar		
		$("#agileUploaderMessages").show();
		$('#agileUploaderMessages').text(opts.maxPostSizeMessage);
		clearTimeout();
		setTimeout('$("#agileUploaderMessages").fadeOut()', 3000);
	}
	
	$.fn.agileUploaderFileLimit = function(lastFile, fileId) {		
		$('#id-'+fileId).remove(); // in case the row was visually added because it had a progress bar		
		$("#agileUploaderMessages").show();
		$('#agileUploaderMessages').text(opts.maxFileMessage);
		clearTimeout();
		setTimeout('$("#agileUploaderMessages").fadeOut()', 3000);
	}

	$.fn.agileUploader = function(options) {			
		opts = $.extend({}, $.fn.agileUploader.defaults, options);    
		opts.flashVars = $.extend({}, $.fn.agileUploader.defaults.flashVars, options.flashVars);    
		opts.flashParams = $.extend({}, $.fn.agileUploader.defaults.flashParams, options.flashParams);
		opts.flashAttributes = $.extend({}, $.fn.agileUploader.defaults.flashAttributes, options.flashAttributes);
		
		return this.each(function() {
			// IE6/7 don't have data uri support
			var data = new Image();
			data.onload = data.onerror = function(){
				if(this.width != 1 || this.height != 1) {				
					noURI = true;
					}
			}
			data.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
			// end data uri check
	
			$('#'+this.id).append('<div id="agileUploaderAttachArea"><div id="agileUploaderEMBED"></div><div id="agileUploaderMessages"></div></div>');
			
			$.fn.agileUploaderEmbed(); // embed
			
			// Add the file queue list
			$('#'+this.id).prepend('<div id="agileUploaderRemoveAll"></div><div id="agileUploaderInfo"><ul id="agileUploaderFileList"></ul></div>');
		});	
	}
	
	$.fn.agileUploaderSingle = function(options) {
		if(typeof(options) == 'undefined') { 
			var options = {};
		}
		// change around defaults for this	
		delete $.fn.agileUploader.defaults.flashVars.button_up;
		delete $.fn.agileUploader.defaults.flashVars.button_over;
		delete $.fn.agileUploader.defaults.flashVars.button_down;
		$.fn.agileUploader.defaults.flashWidth = 310;
		$.fn.agileUploader.defaults.flashHeight = 30;
		$.fn.agileUploader.defaults.flashVars.show_file_input_field = 'true';
		$.fn.agileUploader.defaults.flashVars.show_encode_progress = 'true';
		// combine everything together
		opts = $.extend({}, $.fn.agileUploader.defaults, options);		
		if(typeof(options.flashVars) == 'undefined') { options.flashVars = {}; }
		opts.flashVars = $.extend({}, $.fn.agileUploader.defaults.flashVars, options.flashVars);
		if(typeof(options.flashParams) == 'undefined') { options.flashParams = {}; }
		opts.flashParams = $.extend({}, $.fn.agileUploader.defaults.flashParams, options.flashParams);
		if(typeof(options.flashAttributes) == 'undefined') { options.flashAttributes = {}; }
		opts.flashAttributes = $.extend({}, $.fn.agileUploader.defaults.flashAttributes, options.flashAttributes);
		// always set to -1 so it goes into a single replace mode
		opts.flashVars.file_limit = -1; 
		
		return this.each(function() {
			$('#'+this.id).append('<div id="agileUploaderAttachArea"><div id="agileUploaderEMBED"></div><div id="agileUploaderMessages" class="agileUploaderSingleMessages"></div></div>');
			$.fn.agileUploaderEmbed(); // embed
		});
	}

	$.fn.agileUploaderEmbed = function() {
		// Embed with jQuery Flash if available
		if(typeof($().flash) == 'function') {	
			$('#agileUploaderEMBED').flash({
				// As always; all settings are entirely optional.
			    id: "agileUploaderSWF", 
			    width: opts.flashWidth,
			    height: opts.flashHeight,
			    src: opts.flashSrc,
			    flashvars: opts.flashVars,
			    bgcolor: '#fff',
			    quality: 'high',
			    wmode: 'transparent',
			    allowscriptaccess: 'always',
			    classid: 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000', // For IE support.
			    codebase: 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=', // Ditto.
			    pluginspace: 'http://get.adobe.com/flashplayer', // Download Firefox plugin if missing.
			    version: '10.0.0'		
		     });
		} else 			
		// Embed the swf using swfobject (if swfobject is available)
		if(typeof(swfobject) != 'undefined') {
			swfobject.embedSWF(opts.flashSrc, 'agileUploaderEMBED', opts.flashWidth, opts.flashHeight, "10.0.0","expressInstall.swf", opts.flashVars, opts.flashParams, opts.flashAttributes);
		} else {
			$('#agileUploaderEMBED').html('<p>You need to have either swfobject or jquery flash in order to embed.</p>');
		}
	}
		
	$.fn.agileUploaderSubmit = function() {
		document.getElementById('agileUploaderSWF').sendForm();
	}
	
	$.fn.agileUploader.defaults = {
		// First the Flash embed size and Flashvars (which is another object which makes it easy)
		flashSrc: 'agile-uploader.swf',
		flashWidth: 25,
		flashHeight: 22,
		flashParams: {allowscriptaccess: 'always'},
		flashAttributes: {id: "agileUploaderSWF"},
		flashVars: {
			max_height: 500,
			max_width: 500,
			jpg_quality: 85, 
			preview_max_height: 50,
			preview_max_width: 50,
			show_file_input_field: 'false',
			show_encode_progress: 'false',			
			js_get_form_data: '$.fn.agileUploaderSerializeFormData',
			js_submit_callback: '$.fn.agileUploaderSubmitted',
			js_preview_callback: '$.fn.agileUploaderPreview',
			js_attach_callback: '$.fn.agileUploaderAttachFile',
			js_encode_progress_callback: '$.fn.agileUploaderCurrentEncodeProgress',
			js_max_post_size_callback: '$.fn.agileUploaderMaxPostSize',
			js_file_limit_callback: '$.fn.agileUploaderFileLimit',
			return_submit_response: 'true',
			file_filter: '*.jpg;*.jpeg;*.gif;*.png;*.JPG;*.JPEG;*.GIF;*.PNG',
			file_filter_description: 'Files',
			max_post_size: 1536,
			file_limit: 0,
			return_submit_response: 'true',
			button_up:'add-file.png',
			button_over:'add-file.png',
			button_down:'add-file.png'		
		},
		progressBar: '#000000',
		attachScrollSpeed: 1000,		
		removeIcon: 'trash-icon.png',
		genericFileIcon: 'file-icon.png',
		maxPostSizeMessage: 'Attachments exceed maximum size limit.',
		maxFileMessage: 'File limit hit, try removing a file first.',
		removeAllText: 'remove all'
	}	
	
})(jQuery);
