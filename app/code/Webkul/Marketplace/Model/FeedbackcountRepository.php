<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package Webkul_Marketplace
 * @author Webkul
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Model;

/**
 * FeedbackcountRepository Repo Class
 */
class FeedbackcountRepository implements \Webkul\Marketplace\Api\FeedbackcountRepositoryInterface
{
    /**
     * @var \Webkul\Marketplace\Model\FeedbackcountFactory
     */
    protected $modelFactory = null;
    /**
     * @var \Webkul\Marketplace\Model\ResourceModel\Feedbackcount\CollectionFactory
     */
    protected $collectionFactory = null;

    /**
     * Initialize
     *
     * @param \Webkul\Marketplace\Model\FeedbackcountFactory $modelFactory
     * @param \Webkul\Marketplace\Model\ResourceModel\Feedbackcount\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Webkul\Marketplace\Model\FeedbackcountFactory $modelFactory,
        \Webkul\Marketplace\Model\ResourceModel\Feedbackcount\CollectionFactory $collectionFactory
    ) {
        $this->modelFactory = $modelFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get by id
     *
     * @param int $id
     * @return \Webkul\Marketplace\Model\Feedbackcount
     */
    public function getById($id)
    {
        $model = $this->modelFactory->create()->load($id);
        if (!$model->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('The data with the "%1" ID doesn\'t exist.', $id)
            );
        }
        return $model;
    }

    /**
     * Save
     *
     * @param \Webkul\Marketplace\Model\Feedbackcount $subject
     * @return \Webkul\Marketplace\Model\Feedbackcount
     */
    public function save(\Webkul\Marketplace\Model\Feedbackcount $subject)
    {
        try {
            $subject->save();
        } catch (\Exception $exception) {
             throw new \Magento\Framework\Exception\CouldNotSaveException(__($exception->getMessage()));
        }
        return $subject;
    }

    /**
     * Get list
     *
     * @param Magento\Framework\Api\SearchCriteriaInterface $creteria
     * @return Magento\Framework\Api\SearchResults
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $creteria)
    {
        $collection = $this->collectionFactory->create();
        return $collection;
    }

    /**
     * Delete
     *
     * @param \Webkul\Marketplace\Model\Feedbackcount $subject
     * @return boolean
     */
    public function delete(\Webkul\Marketplace\Model\Feedbackcount $subject)
    {
        try {
            $subject->delete();
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete by id
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
