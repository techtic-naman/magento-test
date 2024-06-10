/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define([
    "jquery",
    'mage/template',
    'Magento_Ui/js/modal/alert',
    "mage/translate",
    "jquery/file-uploader"
    ], function($, mageTemplate, alert){
        $.widget('mage.imageGallery', {
            options: {
            },
            /**
             * Widget initialization
             * @private
             */
            _create: function() {
                var self = this;
                $(self.options.uploadId).fileupload({
                    dataType: 'json',
                    dropZone: '[data-tab-panel=image-management]',
                    sequentialUploads: true,
                    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                    maxFileSize: self.options.maxFileSize,
                    add: function (e, data) {
                        var progressTmpl = mageTemplate(self.options.templateId),
                            fileSize,
                            tmpl;

                        $.each(data.files, function (index, file) {
                            fileSize = typeof file.size == "undefined" ?
                                $.mage.__('We could not detect a size.') :
                                byteConvert(file.size);

                            data.fileId = Math.random().toString(33).substr(2, 18);

                            tmpl = progressTmpl({
                                data: {
                                    name: file.name,
                                    size: fileSize,
                                    id: data.fileId
                                }
                            });

                            $(tmpl).appendTo(self.options.contentUploaderId);
                        });

                        $(this).fileupload('process', data).done(function () {
                            data.submit();
                        });
                    },
                    done: function (e, data) {
                        if (data.result && !data.result.error) {
                            $(self.options.contentId).trigger('addItem', data.result);
                        } else {
                            $('#' + data.fileId)
                                .delay(2000)
                                .hide('highlight');
                            alert({
                            content: $.mage.__('We don\'t recognize or support this file extension type.')
                            });
                        }
                        $('#' + data.fileId).remove();
                    },
                    progress: function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        var progressSelector = '#' + data.fileId + ' .progressbar-container .progressbar';
                        $(progressSelector).css('width', progress + '%');
                    },
                    fail: function (e, data) {
                        var progressSelector = '#' + data.fileId;
                        $(progressSelector).removeClass('upload-progress').addClass('upload-failure')
                            .delay(2000)
                            .hide('highlight')
                            .remove();
                    }
                });
                $(self.options.uploadId).fileupload('option', {
                    process: [{
                        action: 'load',
                        fileTypes: /^image\/(gif|jpeg|png)$/
                    }, {
                        action: 'resize',
                        maxWidth: self.options.maxWidth,
                        maxHeight: self.options.maxHeight 
                    }, {
                        action: 'save'
                    }]
                });
            }
    })
    return $.mage.imageGallery;
});