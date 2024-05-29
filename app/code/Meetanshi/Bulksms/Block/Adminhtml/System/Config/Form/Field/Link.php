<?php

namespace Meetanshi\Bulksms\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Config\Model\Config\CommentInterface;

class Link extends AbstractBlock implements CommentInterface
{
    public function getCommentText($elementValue)
    {
        $url = $this->_urlBuilder->getUrl('bulksms/phonebook/index');
        $csvFile = $this->_assetRepo->getUrl('Meetanshi_Bulksms::csv/sample_import.csv');
        return "<span>.csv supported, You can see the phonebook contacts after import at  </span><a href='$url'>Manage Phonebook.</a> , <a href='$csvFile'>Download Sample Csv.</a> ";
    }
}
