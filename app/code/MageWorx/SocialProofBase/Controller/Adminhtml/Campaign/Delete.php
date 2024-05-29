<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Controller\Adminhtml\Campaign;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;

class Delete extends \MageWorx\SocialProofBase\Controller\Adminhtml\Campaign
{
    /**
     * @return ResultRedirect
     */
    public function execute(): ResultRedirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id             = $this->getRequest()->getParam(CampaignInterface::CAMPAIGN_ID);

        if ($id) {
            try {
                $this->campaignRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The Campaign has been deleted.'));
                $resultRedirect->setPath('mageworx_socialproofbase/*/');

                return $resultRedirect;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The Campaign no longer exists.'));

                return $resultRedirect->setPath('mageworx_socialproofbase/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->critical($e);

                return $resultRedirect->setPath(
                    'mageworx_socialproofbase/campaign/edit',
                    [CampaignInterface::CAMPAIGN_ID => $id]
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the Campaign'));
                $this->logger->critical($e);

                return $resultRedirect->setPath(
                    'mageworx_socialproofbase/campaign/edit',
                    [CampaignInterface::CAMPAIGN_ID => $id]
                );
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a Campaign to delete.'));
        $resultRedirect->setPath('mageworx_socialproofbase/*/');

        return $resultRedirect;
    }
}
