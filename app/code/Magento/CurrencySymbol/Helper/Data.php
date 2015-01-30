<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Currency Symbol helper
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CurrencySymbol\Helper;

class Data extends \Magento\Core\Helper\Data
{
    /**
     * @var \Magento\CurrencySymbol\Model\System\CurrencysymbolFactory
     */
    protected $_symbolFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Store\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\CurrencySymbol\Model\System\CurrencysymbolFactory $symbolFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Store\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $appState,
        \Magento\CurrencySymbol\Model\System\CurrencysymbolFactory $symbolFactory
    ) {
        $this->_symbolFactory = $symbolFactory;
        parent::__construct(
            $context,
            $scopeConfig,
            $storeManager,
            $appState
        );
    }

    /**
     * Get currency display options
     *
     * @param string $baseCode
     * @return array
     */
    public function getCurrencyOptions($baseCode)
    {
        $currencyOptions = [];
        $currencySymbol = $this->_symbolFactory->create();
        if ($currencySymbol) {
            $customCurrencySymbol = $currencySymbol->getCurrencySymbol($baseCode);

            if ($customCurrencySymbol) {
                $currencyOptions['symbol'] = $customCurrencySymbol;
                $currencyOptions['display'] = \Magento\Framework\Currency::USE_SYMBOL;
            }
        }

        return $currencyOptions;
    }
}
