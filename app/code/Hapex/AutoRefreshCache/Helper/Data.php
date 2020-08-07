<?php
namespace Hapex\AutoRefreshCache\Helper;

use Hapex\Core\Helper\DataHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class Data extends DataHelper
{
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {

        parent::__construct($context, $objectManager);
    }

    public function isEnabled()
    {
        return $this->getConfigFlag('hapex_autorefreshcache/general/enable');
    }

    public function isCacheCleanEnabled()
    {
        return $this->getConfigFlag('hapex_autorefreshcache/options/clean_enable');
    }

    public function isCacheFlushEnabled()
    {
        return $this->getConfigFlag('hapex_autorefreshcache/options/flush_enable');
    }

    public function log($message)
    {
        $this->helperLog->printLog("hapex_cache_refresh", $message);
    }
}
