<?php
/**
* Copyright © MageWorx. All rights reserved.
* See LICENSE.txt for license details.
*/

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Block\Adminhtml\Campaign\StepsWizard\Steps;

class EventType extends \Magento\Ui\Block\Component\StepsWizard\StepAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getCaption(): string
    {
        return __('Event Type')->render();
    }
}
