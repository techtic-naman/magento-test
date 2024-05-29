<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule;

class Delete extends \MageWorx\RewardPoints\Controller\Adminhtml\Reward\Rule
{
    /**
     * Delete reward rule action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->ruleRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You have deleted the rule.'));
                $this->_redirect('mageworx_rewardpoints/*/');

                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Sorry, the rule can\'t be deleted. Please, review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('mageworx_rewardpoints/*/edit', ['id' => $this->getRequest()->getParam('id')]);

                return;
            }
        }
        $this->messageManager->addErrorMessage(__('Sorry, it is impossible to find a rule to delete'));
        $this->_redirect('mageworx_rewardpoints/*/');
    }
}
