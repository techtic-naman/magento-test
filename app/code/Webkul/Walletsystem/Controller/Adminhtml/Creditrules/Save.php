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

namespace Webkul\Walletsystem\Controller\Adminhtml\Creditrules;

use Webkul\Walletsystem\Controller\Adminhtml\Creditrules as CreditrulesController;
use Magento\Backend\App\Action;
use Webkul\Walletsystem\Model\WalletcreditrulesFactory;

/**
 * Webkul Walletsystem Class
 */
class Save extends CreditrulesController
{
    /**
     * @var Webkul\Walletsystem\Model\WalletcreditrulesFactory
     */
    private $walletcreditrulesFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * Constructor
     *
     * @param Action\Context $context
     * @param WalletcreditrulesFactory $walletcreditrulesFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        Action\Context $context,
        WalletcreditrulesFactory $walletcreditrulesFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->walletcreditrulesFactory = $walletcreditrulesFactory;
        $this->date = $date;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $error = $this->validateData($data);
            if (!empty($error)) {
                $this->messageManager->addError(__($error[0]));
                return $resultRedirect->setPath('walletsystem/creditrules/creditrules');
            }
            $model = $this->walletcreditrulesFactory->create();
            $id = $this->getRequest()->getParam('entity_id');
            if ($id) {
                $model->load($id);
            } else {
                $duplicate = $this->checkForAlreadyExists($data);
                if ($duplicate) {
                    $this->messageManager->addError(__("A rule with same details already exists."));
                    return $resultRedirect->setPath('walletsystem/creditrules/creditrules');
                }
                $data['created_at'] = $this->date->gmtDate();
            }
            $model->setData($data);
            try {
                $model->save();
                $this->messageManager->addSuccess(
                    __('Credit Rule successfully saved.')
                );
                $this->_session
                    ->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        'walletsystem/creditrules/edit',
                        [
                            'entity_id' => $model->getEntityId(),
                            '_current' => true
                        ]
                    );
                }
                return $resultRedirect->setPath('walletsystem/creditrules/creditrules');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while saving the data.')
                );
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath(
                'walletsystem/creditrules/edit',
                ['entity_id' => $this->getRequest()->getParam('entity_id')]
            );
        }
        return $resultRedirect->setPath('walletsystem/creditrules/creditrules');
    }

    /**
     * Check for already exists
     *
     * @param array $data
     * @return bool
     */
    protected function checkForAlreadyExists($data)
    {
        $creditModel = $this->walletcreditrulesFactory->create()
            ->getCollection()
            ->addFieldToFilter('amount', $data['amount'])
            ->addFieldToFilter('based_on', $data['based_on'])
            ->addFieldToFilter('minimum_amount', $data['minimum_amount'])
            ->addFieldToFilter('start_date', $data['start_date'])
            ->addFieldToFilter('end_date', $data['end_date']);

        if ($creditModel->getSize()) {
            return true;
        }
        return false;
    }

    /**
     * Validate data
     *
     * @param array $data
     * @return array
     */
    protected function validateData($data)
    {
        $error = [];
        if ($data['start_date']>$data['end_date']) {
            $error[] = __("End date can not be lesser then start From date.");
        }
        return $error;
    }
}
