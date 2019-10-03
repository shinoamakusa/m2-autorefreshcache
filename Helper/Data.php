<?php

namespace Hapex\AutoRefreshCache\Helper;

use Hapex\Core\Helper\DataHelper;
use Magento\Framework\App\Helper\Context;

class Data extends DataHelper
{
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }
    
    public function isEnabled()
    {
        return $this->getConfigFlag('hapex_autorefreshcache/general/enable');
    }
    
    public function log($message)
    {
        $this->printLog("hapex_cache_refresh", $message);
    }
}
