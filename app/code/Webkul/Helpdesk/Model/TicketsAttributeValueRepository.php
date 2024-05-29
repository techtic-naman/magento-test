<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Helpdesk
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Model;

use Magento\Framework\Filesystem\Driver\File;

class TicketsAttributeValueRepository implements \Webkul\Helpdesk\Api\TicketsAttributeValueRepositoryInterface
{
    /**
     * @var \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory
     */
    protected $_ticketsCustomAttributesFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    protected $_eavAttribute;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    protected $_ticketsAttributeValueFactory;

    /**
     * @var \Webkul\Helpdesk\Helper\Data
     */
    protected $_helper;

    /**
     * @var File
     */
    protected $_file;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * TicketsAttributeValueRepository constructor.
     *
     * @param \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory $ticketsCustomAttributesFactory
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute    $eavAttribute
     * @param \Webkul\Helpdesk\Model\TicketsAttributeValueFactory   $ticketsAttributeValueFactory
     * @param \Webkul\Helpdesk\Helper\Data                          $helper
     * @param File                                                  $file
     * @param \Magento\MediaStorage\Model\File\UploaderFactory      $fileUploaderFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger                $helpdeskLogger
     * @param \Magento\Framework\Message\ManagerInterface           $messageManager
     */
    public function __construct(
        \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory $ticketsCustomAttributesFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
        \Webkul\Helpdesk\Model\TicketsAttributeValueFactory $ticketsAttributeValueFactory,
        \Webkul\Helpdesk\Helper\Data $helper,
        File $file,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_ticketsCustomAttributesFactory = $ticketsCustomAttributesFactory;
        $this->_eavAttribute = $eavAttribute;
        $this->_ticketsAttributeValueFactory = $ticketsAttributeValueFactory;
        $this->_helper = $helper;
        $this->_file = $file;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->messageManager = $messageManager;
    }

    /**
     * SaveTicketAttributeValues
     *
     * @param  int   $ticketId
     * @param  mixed $wholedata
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveTicketAttributeValues($ticketId, $wholedata)
    {
        if (isset($wholedata['type'])) {
            $attributeIds = $this->_ticketsCustomAttributesFactory->create()
                ->getAllowedTicketCustomerAttributes($wholedata['type']);
        }
        if (isset($attributeIds)) {
            foreach ($attributeIds as $attributeId) {
                $attribute = $this->_eavAttribute->load($attributeId);
                if (isset($wholedata[$attribute['attribute_code']]) && $wholedata[$attribute['attribute_code']] != "") {
                    $ticketAttributeModel = $this->_ticketsAttributeValueFactory->create();
                    if ($attribute['frontend_input'] == 'multiselect') {
                        $ticketAttributeModel->setValue(implode(",", $wholedata[$attribute['attribute_code']]));
                    } else {
                        $ticketAttributeModel->setValue($wholedata[$attribute['attribute_code']]);
                    }
                    if (($attribute['frontend_input'] == 'image' || $attribute['frontend_input'] == 'file') &&
                        !empty($wholedata[$attribute['attribute_code']])) {

                        $uploader = $this->_fileUploaderFactory->create(['fileId' => $attribute['attribute_code']]);
                        $fileExt = $uploader->getFileExtension();
                        $allowedExtensions = ['jpg','jpeg','gif','png','doc','pdf'];
                        if ($attribute['note']) {
                            $allowedConfigExt = str_replace(" ", "", $attribute['note']);
                            $allowedExtensions = explode(',', $allowedConfigExt);
                        }
                        if (!in_array($fileExt, $allowedExtensions)) {
                            $fileName = $wholedata[$attribute['attribute_code']];
                            $this->messageManager->addNoticeMessage(__('File %1 extention not allowed.', $fileName));
                            continue;
                        }
                        $uploader->setAllowedExtensions($allowedExtensions);
                        $mediaPath = $this->_helper->getMediaPath();
                        $path = $mediaPath."/helpdesk/attributeattachment/".$ticketId."/";
                        $this->_file->createDirectory($path);
                        $uploadedFile = $uploader->save($path);
                        if (isset($uploadedFile['file'])) {
                            $ticketAttributeModel->setValue($uploadedFile['file']);
                        }
                    }
                    $ticketAttributeModel->setTicketId($ticketId);
                    $ticketAttributeModel->setAttributeId($attributeId);
                    $ticketAttributeModel->save();
                }
            }
        }
    }

    /**
     * EditTicketAttributeValues save ticket custom attribute values
     *
     * @param  Array $wholedata Post request data
     * @return [type]            [description]
     */
    public function editTicketAttributeValues($wholedata)
    {
        foreach ($wholedata['custom'] as $key => $attributeValue) {
            $attributeId = $this->_eavAttribute->getIdByCode("ticketsystem_ticket", $key);
            $attribute = $this->_eavAttribute->load($attributeId);
            $customAttributeValueCollection = $this->_ticketsAttributeValueFactory->create()->getCollection()
                ->addFieldToFilter("attribute_id", ["eq"=>$attributeId])
                ->addFieldToFilter("ticket_id", ["eq"=>$wholedata['ticket_id']]);

            foreach ($customAttributeValueCollection as $customAttribuevalue) {
                if ($attribute['frontend_input'] == 'multiselect') {
                    $customAttribuevalue->setValue(implode(",", $attributeValue));
                } elseif ($attribute['frontend_input'] == 'image' || $attribute['frontend_input'] == 'file') {

                    $mediaPath = $this->_helper->getMediaPath();
                    $path = $mediaPath."/helpdesk/attributeattachment/".$wholedata['ticket_id']."/";
                    $filePath = $path.$customAttribuevalue->getValue();
                    $this->removeCustomAttributeFile($filePath);

                    $uploader = $this->_fileUploaderFactory->create(['fileId' => $attribute['attribute_code']]);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'doc', 'pdf']);
                    $this->_file->createDirectory($path);
                    $uploadedFile = $uploader->save($path);
                    if (isset($uploadedFile['file'])) {
                        $customAttribuevalue->setValue($uploadedFile['file']);
                    }
                } else {
                    $customAttribuevalue->setValue($attributeValue);
                }
                $customAttribuevalue->save();
            }
        }
    }

    /**
     * Delete customattribute file
     *
     * @param  string $filePath
     */
    private function removeCustomAttributeFile($filePath)
    {
        if ($this->_file->isExists($filePath)) {
            $this->_file->deleteFile($filePath);
        }
    }
}
