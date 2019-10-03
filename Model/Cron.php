<?php 
namespace Hapex\AutoRefreshCache\Model; 
use Magento\Backend\App\Action\Context; 
use Magento\Backend\App\Action; 
use Magento\Framework\App\Cache\Manager as CacheManager; 
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;
class Cron 
{ 
    protected $helperData;
    
    public function __construct(\Magento\Framework\Model\Context $context, \Hapex\AutoRefreshCache\Helper\Data $helperData, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,\Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool) 
    { 
        $this->_cacheTypeList = $cacheTypeList; 
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->helperData = $helperData;
    } 
  
    public function cleanCache() 
    { 
        if ($this->helperData->isEnabled())
        {
            $this->helperData->log("--- Starting Magento Cache Cleaning ---");
            try
            {
                $cache_types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice'); 
                foreach ($cache_types as $type) { 
                    $this->_cacheTypeList->cleanType($type);
                    $this->helperData->log("Adding cache type $type to the cleaning list");
                } 
                
                $this->helperData->log("Processing the cache cleanup list");
                foreach ($this->_cacheFrontendPool as $cache_clean_flush) { 
                    $cache_clean_flush->getBackend()->clean(); 
                }
                $this->helperData->log("Cache cleanup list processed");
            }
            catch (\Exception $e)
            {
                $this->helperData->log("Error: " . $e->getMessage());
            }
            finally
            {
                $this->helperData->log("--- Ending Magento Cache Cleaning ---");
            }
        }
        return $this;
    } 
} 