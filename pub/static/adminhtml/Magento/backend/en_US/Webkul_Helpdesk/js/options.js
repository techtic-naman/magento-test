/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
/*jshint jquery:true*/

/* eslint-disable no-undef */
// jscs:disable jsDoc

define(
    [
    'jquery',
    'mage/template',
    'uiRegistry',
    'jquery/ui',
    'prototype',
    'form',
    'validation'
    ], function (jQuery, mageTemplate, rg) {
        'use strict';

        return function (config) {
            var optionDefaultInputType = 'radio',
            attributeOption = {
                table: $('attribute-options-table'),
                itemCount: 0,
                totalItems: 0,
                rendered: 0,
                template: mageTemplate('#row-template'),
                isReadOnly: config.idReadOnly,
                add: function (data, render) {
                    var isNewOption = false,
                        element;

                    if (typeof data.id == 'undefined') {
                        data = {
                            'id': 'option_' + this.itemCount,
                            'sort_order': this.itemCount + 1
                        };
                        isNewOption = true;
                    }

                    if (!data.intype) {
                        data.intype = optionDefaultInputType;
                    }

                    if (!this.totalItems) {
                        data.checked = 'checked';
                    }
                    element = this.template(
                        {
                            data: data
                        }
                    );

                    if (isNewOption && !this.isReadOnly) {
                        this.enableNewOptionDeleteButton(data.id);
                    }
                    this.itemCount++;
                    this.totalItems++;
                    this.elements += element;

                    if (render) {
                        this.render();
                    }
                },
                remove: function (event) {
                    var element = $(Event.findElement(event, 'tr')),
                        elementFlags; // !!! Button already have table parent in safari

                    // Safari workaround
                    element.ancestors().each(
                        function (parentItem) {
                            if (parentItem.hasClassName('option-row')) {
                                element = parentItem;
                                throw $break;
                            } else if (parentItem.hasClassName('box')) {
                                throw $break;
                            }
                        }
                    );

                    if (element) {
                        elementFlags = element.getElementsByClassName('delete-flag');

                        if (elementFlags[0]) {
                            elementFlags[0].value = 1;
                        }

                        element.addClassName('no-display');
                        element.addClassName('template');
                        element.hide();
                        this.totalItems--;
                        this.updateItemsCountField();
                    }
                },
                updateItemsCountField: function () {
                    $('option-count-check').value = this.totalItems > 0 ? '1' : '';
                },
                enableNewOptionDeleteButton: function (id) {
                    $$('#delete_button_container_' + id + ' button').each(
                        function (button) {
                            button.enable();
                            button.removeClassName('disabled');
                        }
                    );
                },
                bindRemoveButtons: function () {
                    jQuery('#swatch-visual-options-panel').on('click', '.delete-option', this.remove.bind(this));
                },
                render: function () {
                    Element.insert($$('[data-role=options-container]')[0], this.elements);
                    this.elements = '';
                },
                renderWithDelay: function (data, from, step, delay) {
                    var arrayLength = data.length,
                        len;

                    for (len = from + step; from < len && from < arrayLength; from++) {
                        this.add(data[from]);
                    }
                    this.render();

                    if (from === arrayLength) {
                        this.updateItemsCountField();
                        this.rendered = 1;
                        jQuery('body').trigger('processStop');

                        return true;
                    }
                    setTimeout(this.renderWithDelay.bind(this, data, from, step, delay), delay);
                }
            };

            if ($('add_new_option_button')) {
                Event.observe('add_new_option_button', 'click', attributeOption.add.bind(attributeOption, {}, true));
            }

            $('manage-options-panel').on(
                'click', '.delete-option', function (event) {
                    attributeOption.remove(event);
                }
            );

            jQuery(document).ready(
                function () {
                    if (attributeOption.rendered) {
                        return false;
                    }
                    jQuery('body').trigger('processStart');
                    attributeOption.renderWithDelay(config.attributesData, 0, 100, 300);
                    attributeOption.bindRemoveButtons();
                }
            );

            if (config.isSortable) {
                jQuery(
                    function ($) {
                        $('[data-role=options-container]').sortable(
                            {
                                distance: 8,
                                tolerance: 'pointer',
                                cancel: 'input, button',
                                axis: 'y',
                                update: function () {
                                    $('[data-role=options-container] [data-role=order]').each(
                                        function (index, element) {
                                            $(element).val(index + 1);
                                        }
                                    );
                                }
                            }
                        );
                    }
                );
            }

            window.attributeOption = attributeOption;
            window.optionDefaultInputType = optionDefaultInputType;

            rg.set('manage-options-panel', attributeOption);
        };
    }
);
