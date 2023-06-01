<?php

namespace Hapex\AutoRefreshCache\Cron;

use Hapex\Core\Cron\BaseCron;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\TypeListInterface;
use Hapex\AutoRefreshCache\Helper\Data as DataHelper;
use Hapex\Core\Helper\LogHelper;

class CacheRefresh extends BaseCron
{
    protected $_cacheTypeList;
    protected $_cacheFrontendPool;

    public function __construct(DataHelper $helperData, LogHelper $helperLog, TypeListInterface $cacheTypeList, Pool $cacheFrontendPool)
    {
        parent::__construct($helperData, $helperLog);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
    }

    public function cleanCache()
    {
        switch (!$this->isMaintenance && $this->helperData->isEnabled()) {
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

    protected function doCacheClean()
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
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
        }
    }

    protected function cleanCacheTypes($cache_types = [])
    {
        array_walk($cache_types, function ($type) {
            $this->_cacheTypeList->cleanType($type);
            $this->helperData->log("-- Cleaned cache type '$type'");
        });
    }

    protected function doCacheFlush()
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
        } catch (\Throwable $e) {
            $this->helperLog->errorLog(__METHOD__, $this->helperLog->getExceptionTrace($e));
        }
    }

    protected function flushCache()
    {
        $count = 0;
        iterator_apply($this->_cacheFrontendPool, function ($pool) use (&$count) {
            $pool->current()->getBackend()->clean();
            $count++;
            return true;
        }, array($this->_cacheFrontendPool));
        return $count;
    }
}
