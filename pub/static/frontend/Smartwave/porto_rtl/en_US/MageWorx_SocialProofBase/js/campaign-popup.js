/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    /**
     * CampaignPopup constructor.
     *
     * @param config
     * @param element
     */
    var CampaignPopup = function (config, element) {
        this.config = config;
        this.element = element;
        this.stopProcess = false;
        this.currentNumber = 0;
        this.campaignId = null;
        this.itemIds = [];

        this.init = function () {
            this.updateContent();

            return this;
        };

        this.updateContent = function () {
            if (!this.stopProcess) {
                var self = this;

                $.ajax({
                    url: this.config.ajaxUrl,
                    method: 'POST',
                    data: {
                        displayMode: this.config.displayMode,
                        pageType: this.config.pageType,
                        associatedEntityId: this.config.associatedEntityId,
                        campaignId: this.campaignId,
                        itemIds: this.itemIds
                    },
                    success: function (result) {
                        self._processSuccess(result);
                    },
                });
            } else {
                this.hide();
            }
        };

        this._processSuccess = function (result) {
            if (result.success
                && result.maxNumber > this.currentNumber
            ) {
                $(this.element).addClass(result.position).html(result.content);

                $('.mw-sp__close').on('click', this.hideAndStop.bind(this));

                if (result.removeVerified !== '1') {
                    $(this.element).append(this.config.verifiedHtml);
                }

                this.currentNumber++;

                if (this.currentNumber >= result.maxNumber) {
                    this.stopProcess = true;
                }

                if (!_.isUndefined(result.itemId)) {
                    this.itemIds.push(+result.itemId);
                }

                var autoClose  = +result.autoClose;

                if (!this.campaignId) {
                    this.campaignId = result.campaignId;
                    var startDelay  = +result.startDelay;

                    setTimeout(this.show.bind(this), 1000 * startDelay);

                    if (autoClose) {
                        setTimeout(this.updateContent.bind(this), 1000 * (autoClose + startDelay));
                    }
                } else {
                    if (autoClose) {
                        setTimeout(this.updateContent.bind(this), 1000 * autoClose);
                    }
                }
            } else {
                this.hideAndStop();
            }
        };

        this.show = function () {
            $(this.element).show();
        };

        this.hide = function () {
            $(this.element).hide();
        };

        this.hideAndStop = function () {
            this.hide();
            this.stopProcess = true;
        };

        this.init();
    };

    return {
        'campaign-popup': CampaignPopup
    }
});