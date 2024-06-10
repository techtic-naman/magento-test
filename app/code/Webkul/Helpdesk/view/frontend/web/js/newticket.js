/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
/*jshint jquery:true*/
define(
    [
    "jquery",
    'mage/translate',
    'mage/template',
    'mage/adminhtml/wysiwyg/tiny_mce/setup'
    ], function ($, $t, mageTemplate, tiny_mce) {
        'use strict';
        $.widget(
            'mage.newticket', {
                _create: function () {
                    var self = this;

                    if (parseInt(self.options.alloweditor)) {
                        var wysiwygcompany_description = new wysiwygSetup(
                            "query", {
                                "width":"99%",  // defined width of editor
                                "height":"200px", // height of editor
                                "tinymce":{
                                    "height" : 200,
                                    "menubar" : 'insert',
                                    "plugins" : [
                                      'lists link image preview anchor',
                                      'fullscreen',
                                      'insertdatetime media table help'
                                    ],
                                    "toolbar" : 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link | image | table | insertdatetime | preview | help'
                                },
                                files_browser_window_url: "<?=$block->getWysiwygUrl();?>"
                            }
                        );

                        var wysiwygcustom_field = new wysiwygSetup(
                            "customFieldEditor", {
                                "width":"99%",  // defined width of editor
                                "height":"200px", // height of editor
                                "tinymce":{
                                    "height" : 200,
                                    "menubar" : 'insert',
                                    "plugins" : [
                                      'lists link image preview anchor',
                                      'fullscreen',
                                      'insertdatetime media table help'
                                    ],
                                    "toolbar" : 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link | image | table | insertdatetime | preview | help'
                                },
                                files_browser_window_url: "<?=$block->getWysiwygUrl();?>"
                            }
                        );
                        wysiwygcompany_description.setup("exact");
                        wysiwygcustom_field.setup("exact");
                    }

                    $('#save_butn').click(function (e) {
                        if ($("#create_ticket_form").valid()!==false) {
                            if ($('#query_ifr').length) {
                                var desc = $('#query_ifr').contents().find('body').text();
                                $('#query-error').remove();
                                if (desc === "" || desc === null) {
                                    $('#query-error').remove();
                                    $('#query').parent().append('<div class="mage-error" generated="true" id="query-error">This is a required field.</div>');
                                }
                                if (desc !== "" && desc !== null) {
                                    $('#create_ticket_form').submit();
                                } else {
                                    return false;
                                }
                            }
                        }
                    });

                    var customerId = parseInt($('#create_ticket_form #wk_bodymain').find("input[name='customer_id']").val());
                    if (customerId) {
                        setInterval(
                            function () {
                                saveInDraft();
                            }, parseInt(self.options.draftsavetime)
                        );
                        initailizeCustomFields();
                    }

                    function saveInDraft()
                    {
                        try {
                            var fields = $('#create_ticket_form #wk_bodymain li:visible');
                            var formData = {};
                            fields.each(function () {
                                if ($(this).find('select').length) {
                                    let select = $(this).find('select');
                                    formData[select.attr('name')] = select.val();
                                } else if($(this).find("input[type='text']").length) {
                                    let typeText = $(this).find("input[type='text']");
                                    formData[typeText.attr('name')] = typeText.val();
                                } else if($(this).find("input[type='datetime-local']").length) {
                                    let typeDateTime = $(this).find("input[type='datetime-local']");
                                    formData[typeDateTime.attr('name')] = typeDateTime.val();
                                } else if($(this).find("input[type='date']").length) {
                                    let typeDate = $(this).find("input[type='date']");
                                    formData[typeDate.attr('name')] = typeDate.val();
                                }
                                else if($(this).find("textarea").length) {
                                    let typeTextArea = $(this).find("textarea");
                                    if (parseInt(self.options.alloweditor)) {
                                        formData[typeTextArea.attr('name')] = tinymce.get(typeTextArea.attr('id')).getContent();
                                    } else {
                                        formData[typeTextArea.attr('name')] = typeTextArea.val();
                                    }
                                }
                            });
    
                            if (formData && Object.keys(formData).length !== 0) {
                                $.ajax(
                                    {
                                        url : self.options.saveindraftUrl,
                                        type : 'POST',
                                        data : {form_key: self.options.formkey,create_form: true,content:JSON.stringify(formData)},
                                        dataType : 'json',
                                        success : function (content) {
                                           console.log('content== ',content);
                                        }
                                    }
                                );
                                console.log('formData ==> ',formData);
                            }
                        } catch (e) {
                            console.log('saveInDraft : Error => ',e);
                        }
                    }

                    $('#reset_ticket_form').click(function (e) {
                        $.ajax(
                            {
                                url : self.options.resetPageUrl,
                                type : 'GET',
                                success : function () {
                                    location.reload();
                                }
                            }
                        );
                    });

                    $(".hide_attribute").parents("li").hide();
                    $("#help_type").on(
                        "change",function () {
                            var this_this = $(this);
                            $(".custom_attribute select ,.custom_attribute input ,.custom_attribute textarea") .each(
                                function () {
                                    if ($(this).attr("for") != "") {
                                        if (this_this.val() == $(this).attr("for")) {
                                            $(this).parents("li").show();
                                            $(this).addClass($(this).attr("data-hide-validation"));
                                        } else {
                                            $(this).parents("li").hide();
                                            $(this).removeClass($(this).attr("data-hide-validation"));
                                        }
                                    }
                                }
                            );
                        }
                    );

                    function initailizeCustomFields()
                    {
                        var helpType = $('#create_ticket_form #wk_bodymain #help_type');
                        if (helpType.val()) {
                            $(".custom_attribute select ,.custom_attribute input ,.custom_attribute textarea") .each(
                                function () {
                                    if ($(this).attr("for") != "") {
                                        if (helpType.val() == $(this).attr("for")) {
                                            $(this).removeClass('hide_attribute');
                                            $(this).parents("li").show();
                                            $(this).addClass($(this).attr("data-hide-validation"));
                                        } else {
                                            $(this).parents("li").hide();
                                            $(this).removeClass($(this).attr("data-hide-validation"));
                                        }
                                    }
                                }
                            );
                        }
                    }

                    var allowedExtension = self.options.allowedextensions;
                    $('#wk_ts_file').on(
                        'change',function (e) {
                            var this_this = $(this);
                            if ($(this).get(0).files.length > parseInt(1)) {
                                e.stopImmediatePropagation();
                                $(this).val('');
                                alert($t("Can not upload more than "+1+" images"));
                            } else {
                                var flag = 0;
                                var files = e.originalEvent.target.files;
                                for (var i = 0, len = files.length; i < len; i++) {
                                    var extension = files[i].name.substr((files[i].name.lastIndexOf('.') +1));
                                    if (allowedExtension.indexOf(extension) < 0) {
                                        flag = 1;
                                    }
                                }
                                if (flag) {
                                    this_this.val('');
                                    alert($t("File type not allowed,Allowed file: "+allowedExtension));
                                }
                            }
                        }
                    );
                }
            }
        );
        return $.mage.newticket;
    }
);
