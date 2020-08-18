<?php

namespace Hapex\AutoRefreshCache\Cron;

use Hapex\AutoRefreshCache\Helper\Data as DataHelper;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Model\Context;

class CacheRefresh
{
    protected $helperData;
    protected $context;

    public function __construct(Context $context, DataHelper $helperData, TypeListInterface $cacheTypeList, Pool $cacheFrontendPool)
    {
        $this->context = $context;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->helperData = $helperData;
    }

    public function cleanCache()
    {
        switch ($this->helperData->isEnabled()) {
            case true:
                $this->helperData->log("");
                $this->helperData->log("Starting Auto Cache Refresh");
                $this->doCacheClean();
                $this->doCacheFlush();
                $this->helperData->log("Ending Auto Cache Refresh");
                break;

            default:
                $this->helperData->log("Hapex Auto Cache Refresh is disabled");
                break;
        }
        return $this;
    }

    private function doCacheClean()
    {
        try {
            //$cache_types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
            $this->helperData->log("- Getting cache types list");
            $cache_types = array_keys($this->_cacheTypeList->getTypes());
            $total = count($cache_types);
            $this->helperData->log("- Found $total cache types");

            switch ($this->helperData->isCacheCleanEnabled()) {
                case true:
                    $this->helperData->log("- Cleaning the cache");
                    $this->cleanCacheTypes($cache_types);
                    $this->helperData->log("- Cleaned the cache");
                    break;

                default:
                    $this->helperData->log("- Cache cleaning is disabled");
                    break;
            }
        } catch (\Exception $e) {
            $this->helperData->errorLog(__METHOD__, $e->getMessage());
        }
    }

    private function cleanCacheTypes($cache_types = [])
    {
        foreach ($cache_types as $type) {
            $this->_cacheTypeList->cleanType($type);
            $this->helperData->log("-- Cleaned cache type '$type'");
        }
    }

    private function doCacheFlush()
    {
        try {
            switch ($this->helperData->isCacheFlushEnabled()) {
                case true:
                    $this->helperData->log("- Flushing the cache");
                    $count = $this->flushCache();
                    $count = $count != 0 ? $count : "No";
                    $this->helperData->log("- Flushed $count cache types");
                    break;

                default:
                    $this->helperData->log("- Cache flushing is disabled");
                    break;
            }
        } catch (\Exception $e) {
            $this->helperData->errorLog(__METHOD__, $e->getMessage());
        }
    }

    private function flushCache()
    {
        $count = 0;
        foreach ($this->_cacheFrontendPool as $cache_clean_flush) {
            $cache_clean_flush->getBackend()->clean();
            $count++;
        }
        return $count;
    }
}
