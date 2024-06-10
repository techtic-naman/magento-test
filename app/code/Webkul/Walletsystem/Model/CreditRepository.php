<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_Walletsystem
 * @author Webkul
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\Walletsystem\Model;

use Webkul\Walletsystem\Model\WalletcreditrulesFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Webkul Walletsystem Class
 */
class CreditRepository implements \Webkul\Walletsystem\Api\WalletCreditRepositoryInterface
{
    /**
     * @var Webkul\Walletsystem\Model\WalletcreditrulesFactory
     */
    protected $walletcreditrulesFactory;

    /**
     * Initialize dependencies
     *
     * @param WalletcreditrulesFactory $walletcreditrulesFactory
     */
    public function __construct(
        WalletcreditrulesFactory $walletcreditrulesFactory
    ) {
        $this->walletcreditrulesFactory = $walletcreditrulesFactory;
    }

    /**
     * Save creditRule.
     *
     * @param Webkul\Walletsystem\Api\Data\WalletCreditRuleInterface $creditRule
     * @return Webkul\Walletsystem\Api\Data\WalletCreditRuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Webkul\Walletsystem\Api\Data\WalletCreditRuleInterface $creditRule)
    {
        $creditRuleModel = null;
        if ($creditRule->getEntityId()) {
            $creditRuleModel = $this->walletcreditrulesFactory->create()->load($creditRule->getEntityId());
        }
        if ($creditRuleModel === null) {
            $creditRuleModel = $this->walletcreditrulesFactory->create();
            $creditRuleModel->addData($creditRule);
        } else {
            $creditRuleModel->addData($creditRule);
        }
        $creditRuleId = $creditRuleModel->save()->getEntityId();
        return $this->walletcreditrulesFactory->create()->load($creditRuleId);
    }

    /**
     * Get By Id
     *
     * @param int $creditRuleId
     * @return array
     */
    public function getById($creditRuleId)
    {
        if (!$creditRuleId) {
            throw new \Magento\Framework\Exception\InputException(__('Credit Rule Id required'));
        }
        $creditRuleModel = $this->walletcreditrulesFactory->create();
        $creditRuleModel->load($creditRuleId);
        if (!$creditRuleModel->getEntityId()) {
            throw new NoSuchEntityException(__('Requested Rule doesn\'t exist'));
        }
        return $creditRuleModel;
    }

    /**
     * Delete credit rule.
     *
     * @param \Webkul\Walletsystem\Api\Data\WalletCreditRuleInterface $creditRule
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Webkul\Walletsystem\Api\Data\WalletCreditRuleInterface $creditRule)
    {
        try {
            $creditRuleId = $creditRule->getEntityId();
            $creditRule->delete();
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to remove credit rule %1', $creditRuleId)
            );
        }
        return true;
    }

    /**
     * Delete credit rule by credit rule ID.
     *
     * @param int $creditRuleId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($creditRuleId)
    {
        $creditRuleModel = $this->getById($creditRuleId);
        $this->delete($creditRuleModel);
        return true;
    }
}
