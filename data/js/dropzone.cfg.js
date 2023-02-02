Dropzone.options.documentUpload = {
	method: "post",
	url: 'Upload',
	paramName: "file",
	maxFilesize: 100,
	autoProcessQueue: true,
	acceptedFiles: "application/pdf",
	createImageThumbnails: false,
	
	dictDefaultMessage: "Drop files here to upload",
	dictFallbackMessage: "Your browser does not support drag'n'drop file uploads.",
	dictFallbackText: "Please use the fallback form below to upload your files like in the olden days.",
	dictFileTooBig: "File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.",
	dictInvalidFileType: "Es werden nur PDF-Dateien unterstützt!",
	dictResponseError: "Server responded with {{statusCode}} code.",
	dictCancelUpload: "Upload abgebrochen",
	dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?",
	dictRemoveFile: "Remove file",
	dictRemoveFileConfirmation: null,
	dictMaxFilesExceeded: "You can not upload any more files.",
	
	previewTemplate: "<div class=\"dz-preview dz-file-preview\">\n  <div class=\"dz-image\"><img data-dz-thumbnail /></div>\n  <div class=\"dz-details\">\n    <div class=\"dz-size\"><span data-dz-size></span></div>\n    <div class=\"dz-filename\"><span data-dz-name></span></div>\n  </div>\n  <div class=\"dz-progress\"><span class=\"dz-upload\" data-dz-uploadprogress></span></div>\n  <div class=\"dz-error-message\"><span data-dz-errormessage></span></div>\n  <div class=\"dz-success-mark\">\n</div>\n  <div class=\"dz-error-mark\">\n</div>\n</div>",

	success: function(file, response) {
		
		resp = JSON.parse(response);

		if(resp.code == 200){
		
			return file.previewElement.classList.add("dz-success");
			
		}else if (resp.code == 400){

			var node, _i, _len, _ref, _results;
			var message = resp.msg;
			
			file.previewElement.classList.add("dz-error");
			_ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
			_results = [];
			for (_i = 0, _len = _ref.length; _i < _len; _i++) {
				
				node = _ref[_i];
				_results.push(node.textContent = message);
				
			}
			
			return _results;
			
		}
	},
	/*
	init: function() {
		
        this.on("success", function(file, responseText) {
            console.log(responseText);
            console.log('s');
        });
		
        this.on("error", function(file, responseText) {
            console.log(responseText);
            console.log('e');
        });
    }
	*/
	
};
