<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_Marketplace
 * @author Webkul
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Controller\Adminhtml\Sellerflag;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @var \Webkul\Marketplace\Model\SellerFlagReasonFactory
     */
    protected $sellerFlagFactory;

    /**
     * @var \Webkul\Marketplace\Api\SellerFlagReasonRepositoryInterface
     */
    protected $sellerFlagRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

   /**
    * Construct
    *
    * @param \Magento\Backend\App\Action\Context $context
    * @param \Webkul\Marketplace\Model\SellerFlagReasonFactory $sellerFlagFactory
    * @param \Webkul\Marketplace\Api\SellerFlagReasonRepositoryInterface $sellerFlagRepository
    * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
    * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\Marketplace\Model\SellerFlagReasonFactory $sellerFlagFactory,
        \Webkul\Marketplace\Api\SellerFlagReasonRepositoryInterface $sellerFlagRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->sellerFlagFactory = $sellerFlagFactory;
        $this->sellerFlagRepository = $sellerFlagRepository;
        $this->_date = $date;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('entity_id');

            $model = $this->sellerFlagFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This flag no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setReason($data['reason']);
            $model->setStatus($data['status']);
            if (!$model->getId()) {
                $model->setCreatedAt($this->_date->gmtDate());
            }
            
            try {
                $this->sellerFlagRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the seller flag reason.'));
                $this->dataPersistor->clear('sellerflagreason');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e->getMessage());
            }

            $this->dataPersistor->set('sellerflagreason', $data);
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->getRequest()->getParam('entity_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check for is allowed.
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Marketplace::sellerflag');
    }
}
