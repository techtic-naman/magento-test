<?php
namespace MageWorx\XReviewBase\Block\Review\Customer\Product\Review;

/**
 * Interceptor class for @see \MageWorx\XReviewBase\Block\Review\Customer\Product\Review
 */
class Interceptor extends \MageWorx\XReviewBase\Block\Review\Customer\Product\Review implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Review\Model\ReviewFactory $reviewFactory, \Magento\Review\Model\Rating\Option\VoteFactory $voteFactory, \Magento\Review\Model\RatingFactory $ratingFactory, \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer, \MageWorx\XReviewBase\Model\ConfigProvider $configProvider, \Magento\Framework\Serialize\SerializerInterface $serializer, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $productRepository, $reviewFactory, $voteFactory, $ratingFactory, $currentCustomer, $configProvider, $serializer, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getImage');
        return $pluginInfo ? $this->___callPlugins('getImage', func_get_args(), $pluginInfo) : parent::getImage($product, $imageId, $attributes);
    }
}
