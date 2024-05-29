<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;

class Delete extends \MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer
{
    /**
     * @return ResultRedirect
     */
    public function execute(): ResultRedirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id             = $this->getRequest()->getParam(CountdownTimerInterface::COUNTDOWN_TIMER_ID);

        if ($id) {
            try {
                $this->countdownTimerRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The Countdown Timer has been deleted.'));
                $resultRedirect->setPath('mageworx_countdowntimersbase/*/');

                return $resultRedirect;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The Countdown Timer no longer exists.'));

                return $resultRedirect->setPath('mageworx_countdowntimersbase/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->critical($e);

                return $resultRedirect->setPath(
                    'mageworx_countdowntimersbase/countdownTimer/edit',
                    [CountdownTimerInterface::COUNTDOWN_TIMER_ID => $id]
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the Countdown Timer'));
                $this->logger->critical($e);

                return $resultRedirect->setPath(
                    'mageworx_countdowntimersbase/countdownTimer/edit',
                    [CountdownTimerInterface::COUNTDOWN_TIMER_ID => $id]
                );
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a Countdown Timer to delete.'));
        $resultRedirect->setPath('mageworx_countdowntimersbase/*/');

        return $resultRedirect;
    }
}
