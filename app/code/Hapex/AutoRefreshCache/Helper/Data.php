<?php

namespace Hapex\AutoRefreshCache\Helper;

use Hapex\Core\Helper\DataHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class Data extends DataHelper
{
    protected const XML_PATH_CONFIG_ENABLED = "hapex_autorefreshcache/general/enable";
    protected const XML_PATH_CONFIG_ENABLED_CLEAN = "hapex_autorefreshcache/options/clean_enable";
    protected const XML_PATH_CONFIG_ENABLED_FLUSH = "hapex_autorefreshcache/options/flush_enable";
    protected const FILE_PATH_LOG = "hapex_cache_refresh";

    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {

        parent::__construct($context, $objectManager);
    }

    public function isEnabled()
    {
        return $this->getConfigFlag(self::XML_PATH_CONFIG_ENABLED);
    }

    public function isCacheCleanEnabled()
    {
        return $this->getConfigFlag(self::XML_PATH_CONFIG_ENABLED_CLEAN);
    }

    public function isCacheFlushEnabled()
    {
        return $this->getConfigFlag(self::XML_PATH_CONFIG_ENABLED_FLUSH);
    }

    public function log($message)
    {
        $this->helperLog->printLog(self::FILE_PATH_LOG, $message);
    }
}
