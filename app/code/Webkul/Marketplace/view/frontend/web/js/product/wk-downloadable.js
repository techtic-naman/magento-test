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
    'jquery',
    'uiRegistry',
    'mage/template',
    'Magento_Ui/js/modal/alert',
    'mage/mage',
    'prototype'
], function (jQuery, registry, mageTemplate, alert) {
        jQuery.widget('mage.wkDownloadable', {
            options: {
            },
            /**
             * Widget initialization
             * @private
             */
            _create: function() {
                var self = this;
            var uploaderTemplate = '<div class="no-display" id="[[idName]]-template">' +
                                    '</div>' +
                                        '<div class="no-display" id="[[idName]]-template-progress">' +
                                        '<%- data.percent %>% <%- data.uploaded %> / <%- data.total %>' +
                                        '</div>',

            fileListTemplate = '<span class="file-info">' +
                                        '<span class="file-info-name"><%= data.name %></span>' +
                                        ' ' +
                                        '<span class="file-info-size">(<%- data.size %>)</span>' +
                                    '</span>';
            window.Downloadable = {
                uploaderObj : $H({}),
                objCount : 0,
                setUploaderObj : function(type, key, obj){
                    if (!this.uploaderObj.get(type)) {
                        this.uploaderObj.set(type, $H({}));
                    }
                    this.uploaderObj.get(type).set(key, obj);
                },
                getUploaderObj : function(type, key){
                    try {
                        return this.uploaderObj.get(type).get(key);
                    } catch (e) {
                        try {
                            console.log(e);
                        } catch (e2) {
                            alert({
                                content: e.name + '\n' + e.message
                            });
                        }
                    }
                },
                unsetUploaderObj : function(type, key){
                    try {
                        this.uploaderObj.get(type).unset(key);
                    } catch (e) {
                        try {
                            console.log(e);
                        } catch (e2) {
                            alert({
                                content: e.name + '\n' + e.message
                            });
                        }
                    }
                },
                massUploadByType : function(type){
                    try {
                        this.uploaderObj.get(type).each(function(item){
                            container = item.value.container.up('tr');
                            if (container.visible() && !container.hasClassName('no-display')) {
                                item.value.upload();
                            } else {
                                Downloadable.unsetUploaderObj(type, item.key);
                            }
                        });
                    } catch (e) {
                        try {
                            console.log(e);
                        } catch (e2) {
                            alert({
                                content: e.name + '\n' + e.message
                            });
                        }
                    }
                }
            };

            Downloadable.FileUploader = Class.create();
            Downloadable.FileUploader.prototype = {
                type : null,
                key : null, //key, identifier of uploader obj
                elmContainer : null, //insert Flex object and templates to elmContainer
                fileValueName : null, //name of field of JSON data of saved file
                fileValue : null,
                idName : null, //id name of elements for unique uploader
                uploaderText: uploaderTemplate,
                uploaderSyntax : /(^|.|\r|\n)(\[\[(\w+)\]\])/,
                uploaderObj : $H({}),
                config : null,
                initialize: function (type, key, elmContainer, fileValueName, fileValue ,idName, config) {
                    this.type = type;
                    this.key = key;
                    this.elmContainer = elmContainer;
                    this.fileValueName = fileValueName;
                    this.fileValue = fileValue;
                    this.idName = idName;
                    this.config = config;
                    uploaderTemplate = new Template(this.uploaderText, this.uploaderSyntax);
                    Element.insert(
                        elmContainer,
                        {'top' : uploaderTemplate.evaluate({
                                'idName' : this.idName,
                                'fileValueName' : this.fileValueName,
                                'uploaderObj' : 'Downloadable.getUploaderObj(\''+this.type+'\', \''+this.key+'\')'
                            })
                        }
                    );
                    var elementSave = $(this.idName+'_save');
                    if (elementSave) {
                        elementSave.value = this.fileValue.toJSON
                            ? this.fileValue.toJSON()
                            : Object.toJSON(this.fileValue);
                    }
                    Downloadable.setUploaderObj(
                        this.type,
                        this.key,
                        null
                    );
                    new Downloadable.FileList(this.idName, null);
                }
            };

            Downloadable.FileList = Class.create();
            Downloadable.FileList.prototype = {
                file: [],
                containerId: '',
                container: null,
                uploader: null,
                fileListTemplate: fileListTemplate,
                listTemplate : null,
                initialize: function (containerId, uploader) {
                    this.containerId  = containerId,
                    this.container = $(this.containerId);
                    this.uploader = uploader;
                    this.file = this.getElement('save').value.evalJSON();
                    this.listTemplate = mageTemplate(this.fileListTemplate);
                    this.updateFiles();
                },
                handleFileRemoveAll: function(fileId) {
                    $(this.containerId+'-new').hide();
                    $(this.containerId+'-old').show();
                },
                handleFileSelect: function() {
                    $(this.containerId+'_type').checked = true;
                },
                getElement: function (name) {
                    return $(this.containerId + '_' + name);
                },
                handleUploadComplete: function (file) {
                    if (file.error) {
                        return;
                    }

                    var newFile = {};
                    newFile.file = file.file;
                    newFile.name = file.name;
                    newFile.size = file.size;
                    newFile.status = 'new';
                    this.file[0] = newFile;
                    this.updateFiles();
                },
                updateFiles: function() {
                    this.getElement('save').value = this.file.toJSON
                        ? this.file.toJSON()
                        : Object.toJSON(this.file);
                    this.file.each(function(row){
                        row.size = byteConvert(row.size);
                        $(this.containerId + '-old').innerHTML = this.listTemplate({data: row});
                        $(this.containerId + '-new').hide();
                        $(this.containerId + '-old').show();
                    }.bind(this));
                }
            };

            var alertAlreadyDisplayed = false;

            window.uploaderTemplate = uploaderTemplate;

            window.alertAlreadyDisplayed = alertAlreadyDisplayed;

            registry.set('downloadable', Downloadable);
        }
    });
    return jQuery.mage.wkDownloadable;
});