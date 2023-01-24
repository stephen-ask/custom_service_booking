jQuery(function () {
  'use strict';

  var console = window.console || { log: function () {} };
  var URL = window.URL || window.webkitURL;
  var $image = jQuery('#image');
  var $coverimage = jQuery('#coverimage');
  var $download = jQuery('#download');
  var $dataX = jQuery('#dataX');
  var $dataY = jQuery('#dataY');
  var $dataHeight = jQuery('#dataHeight');
  var $dataWidth = jQuery('#dataWidth');
  
  var $coverdataHeight = jQuery('#coverdataHeight');
  var $coverdataWidth = jQuery('#coverdataWidth');
  
  var $dataRotate = jQuery('#dataRotate');
  var $dataScaleX = jQuery('#dataScaleX');
  var $dataScaleY = jQuery('#dataScaleY');
  var options = {
    aspectRatio: NaN,
    preview: '.profile-preview',
    crop: function (e) {
      $dataX.val(Math.round(e.detail.x));
      $dataY.val(Math.round(e.detail.y));
      $dataHeight.val(Math.round(e.detail.height));
      $dataWidth.val(Math.round(e.detail.width));
      $dataRotate.val(e.detail.rotate);
      $dataScaleX.val(e.detail.scaleX);
      $dataScaleY.val(e.detail.scaleY);
    }
  };
  
  var coveroptions = {
    aspectRatio: NaN,
    preview: '.cover-preview',
    crop: function (e) {
      $dataX.val(Math.round(e.detail.x));
      $dataY.val(Math.round(e.detail.y));
      $coverdataHeight.val(Math.round(e.detail.height));
      $coverdataWidth.val(Math.round(e.detail.width));
      $dataRotate.val(e.detail.rotate);
      $dataScaleX.val(e.detail.scaleX);
      $dataScaleY.val(e.detail.scaleY);
    }
  };
  var originalImageURL = $image.attr('src');
  var originalCoverImageURL = $coverimage.attr('src');
  
  var uploadedImageName = 'cropped.jpg';
  var uploadedImageType = 'image/jpeg';
  var uploadedImageURL;

  // Tooltip
  jQuery('[data-toggle="tooltip"]').tooltip();

  // Cropper
  $image.on({
    ready: function (e) {
      console.log(e.type);
    },
    cropstart: function (e) {
      console.log(e.type, e.detail.action);
    },
    cropmove: function (e) {
      console.log(e.type, e.detail.action);
    },
    cropend: function (e) {
      console.log(e.type, e.detail.action);
    },
    crop: function (e) {
      console.log(e.type);
    },
    zoom: function (e) {
      console.log(e.type, e.detail.ratio);
    }
  }).cropper(options);
  
  $coverimage.on({
    ready: function (e) {
      console.log(e.type);
    },
    cropstart: function (e) {
      console.log(e.type, e.detail.action);
    },
    cropmove: function (e) {
      console.log(e.type, e.detail.action);
    },
    cropend: function (e) {
      console.log(e.type, e.detail.action);
    },
    crop: function (e) {
      console.log(e.type);
    },
    zoom: function (e) {
      console.log(e.type, e.detail.ratio);
    }
  }).cropper(coveroptions);

  // Buttons
  if (!jQuery.isFunction(document.createElement('canvas').getContext)) {
    jQuery('button[data-method="getCroppedCanvas"]').prop('disabled', true);
  }

  if (typeof document.createElement('cropper').style.transition === 'undefined') {
    jQuery('button[data-method="rotate"]').prop('disabled', true);
    jQuery('button[data-method="scale"]').prop('disabled', true);
  }

  // Download
 /* if (typeof $download[0].download === 'undefined') {
    $download.addClass('disabled');
  }*/

  // Options
  jQuery('.docs-toggles').on('change', 'input', function () {
    var $this = jQuery(this);
    var name = $this.attr('name');
    var type = $this.prop('type');
    var cropBoxData;
    var canvasData;

    if (!$image.data('cropper')) {
      return;
    }

    if (type === 'checkbox') {
      options[name] = $this.prop('checked');
      cropBoxData = $image.cropper('getCropBoxData');
      canvasData = $image.cropper('getCanvasData');

      options.ready = function () {
        $image.cropper('setCropBoxData', cropBoxData);
        $image.cropper('setCanvasData', canvasData);
      };
    } else if (type === 'radio') {
      options[name] = $this.val();
    }

    $image.cropper('destroy').cropper(options);
	
  });

  // Methods
  jQuery('.docs-buttons').on('click', '[data-method]', function () {
    var $this = jQuery(this);
    var data = $this.data();
    var cropper = $image.data('cropper');
    var cropped;
    var $target;
    var result;

    if ($this.prop('disabled') || $this.hasClass('disabled')) {
      return;
    }

    if (cropper && data.method) {
      data = jQuery.extend({}, data); // Clone a new one

      if (typeof data.target !== 'undefined') {
        $target = jQuery(data.target);

        if (typeof data.option === 'undefined') {
          try {
            data.option = JSON.parse($target.val());
          } catch (e) {
            console.log(e.message);
          }
        }
      }

      cropped = cropper.cropped;

      switch (data.method) {
        case 'rotate':
          if (cropped && options.viewMode > 0) {
            $image.cropper('clear');
          }

          break;

        case 'getCroppedCanvas':
          if (uploadedImageType === 'image/jpeg') {
            if (!data.option) {
              data.option = {};
            }

            data.option.fillColor = '#fff';
          }

          break;
      }

      result = $image.cropper(data.method, data.option, data.secondOption);

      switch (data.method) {
        case 'rotate':
          if (cropped && options.viewMode > 0) {
            $image.cropper('crop');
          }

          break;

        case 'scaleX':
        case 'scaleY':
          jQuery(this).data('option', -data.option);
          break;

        case 'getCroppedCanvas':
          if (result) {
            // Bootstrap's Modal
            //jQuery('#getCroppedCanvasModal').modal().find('.modal-body').html(result);
			jQuery('#croppedimage').val(result.toDataURL(uploadedImageType));
			
			jQuery('.cropped').html(result);
			
			jQuery('.sf-member-saveimg').removeClass('hide');
			
			var $imagedata = $image.cropper('getData');
			
			jQuery('form input[name="minwidth"]').val($imagedata.width);
			jQuery('form input[name="minheight"]').val($imagedata.height);
          }

          break;

        case 'destroy':
          if (uploadedImageURL) {
            URL.revokeObjectURL(uploadedImageURL);
            uploadedImageURL = '';
            $image.attr('src', originalImageURL);
          }

          break;
      }

      if (jQuery.isPlainObject(result) && $target) {
        try {
          $target.val(JSON.stringify(result));
        } catch (e) {
          console.log(e.message);
        }
      }
    }
  });
  
  // Methods
  jQuery('.cover-buttons').on('click', '[data-method]', function () {
    var $this = jQuery(this);
    var data = $this.data();
    var cropper = $coverimage.data('cropper');
    var cropped;
    var $target;
    var result;

    if ($this.prop('disabled') || $this.hasClass('disabled')) {
      return;
    }

    if (cropper && data.method) {
      data = jQuery.extend({}, data); // Clone a new one

      if (typeof data.target !== 'undefined') {
        $target = jQuery(data.target);

        if (typeof data.option === 'undefined') {
          try {
            data.option = JSON.parse($target.val());
          } catch (e) {
            console.log(e.message);
          }
        }
      }

      cropped = cropper.cropped;

      switch (data.method) {
        case 'rotate':
          if (cropped && coveroptions.viewMode > 0) {
            $coverimage.cropper('clear');
          }

          break;

        case 'getCroppedCanvas':
          if (uploadedImageType === 'image/jpeg') {
            if (!data.option) {
              data.option = {};
            }

            data.option.fillColor = '#fff';
          }

          break;
      }

      result = $coverimage.cropper(data.method, data.option, data.secondOption);

      switch (data.method) {
        case 'rotate':
          if (cropped && coveroptions.viewMode > 0) {
            $coverimage.cropper('crop');
          }

          break;

        case 'scaleX':
        case 'scaleY':
          jQuery(this).data('option', -data.option);
          break;

        case 'getCroppedCanvas':
          if (result) {
            // Bootstrap's Modal
            //jQuery('#getCroppedCanvasModal').modal().find('.modal-body').html(result);
			jQuery('#croppedcoverimage').val(result.toDataURL(uploadedImageType));
			
			jQuery('.covercropped').html(result);
			
			var $coverimagedata = $coverimage.cropper('getData');
			
			jQuery('form input[name="minwidth"]').val($coverimagedata.width);
			jQuery('form input[name="minheight"]').val($coverimagedata.height);
          }

          break;

        case 'destroy':
          if (uploadedImageURL) {
            URL.revokeObjectURL(uploadedImageURL);
            uploadedImageURL = '';
            $coverimage.attr('src', originalCoverImageURL);
          }

          break;
      }

      if (jQuery.isPlainObject(result) && $target) {
        try {
          $target.val(JSON.stringify(result));
        } catch (e) {
          console.log(e.message);
        }
      }
    }
  });

  // Keyboard
  jQuery(document.body).on('keydown', function (e) {
    if (e.target !== this || !$image.data('cropper') || !$coverimage.data('cropper') || this.scrollTop > 300) {
      return;
    }

    switch (e.which) {
      case 37:
        e.preventDefault();
        $image.cropper('move', -1, 0);
		$coverimage.cropper('move', -1, 0);
        break;

      case 38:
        e.preventDefault();
        $image.cropper('move', 0, -1);
		$coverimage.cropper('move', 0, -1);
        break;

      case 39:
        e.preventDefault();
        $image.cropper('move', 1, 0);
		$coverimage.cropper('move', 1, 0);
        break;

      case 40:
        e.preventDefault();
        $image.cropper('move', 0, 1);
		$coverimage.cropper('move', 0, 1);
        break;
    }
  });

  // Import image
  var $inputImage = jQuery('#file-upload');
  
  var $inputcoverImage = jQuery('#cover-upload');

  if (URL) {
    $inputImage.change(function () {
      var files = this.files;
      var file;

      if (!$image.data('cropper')) {
        return;
      }

      if (files && files.length) {
        file = files[0];
		
		jQuery('.sf-member-saveimg').removeClass('hide');

        if (/^image\/\w+$/.test(file.type)) {
          uploadedImageName = file.name;
          uploadedImageType = file.type;

          if (uploadedImageURL) {
            URL.revokeObjectURL(uploadedImageURL);
          }

          uploadedImageURL = URL.createObjectURL(file);
          $image.cropper('destroy').attr('src', uploadedImageURL).cropper(options);
		  
		  jQuery('form input[name="minwidth"]').val('');
		  jQuery('form input[name="minheight"]').val('');
		  
          //$inputImage.val('');
        } else {
          window.alert('Please choose an image file.');
        }
      }
    });
	
	$inputcoverImage.change(function () {
      var files = this.files;
      var file;

      if (!$coverimage.data('cropper')) {
        return;
      }

      if (files && files.length) {
        file = files[0];
		
		jQuery('.sf-member-saveimg').removeClass('hide');

        if (/^image\/\w+$/.test(file.type)) {
          uploadedImageName = file.name;
          uploadedImageType = file.type;

          if (uploadedImageURL) {
            URL.revokeObjectURL(uploadedImageURL);
          }

          uploadedImageURL = URL.createObjectURL(file);
          $coverimage.cropper('destroy').attr('src', uploadedImageURL).cropper(coveroptions);
		  
		  jQuery('form input[name="coverminwidth"]').val('');
		  jQuery('form input[name="coverminheight"]').val('');
		  
          //$inputImage.val('');
        } else {
          window.alert('Please choose an image file.');
        }
      }
    });
  } else {
    //$inputImage.prop('disabled', true).parent().addClass('disabled');
  }
});
