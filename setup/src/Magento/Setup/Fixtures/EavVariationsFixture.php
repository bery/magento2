<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Fixtures;

/**
 * Class EavVariationsFixture
 */
class EavVariationsFixture extends Fixture
{
    /**
     * @var int
     */
    protected $priority = 40;

    const ATTRIBUTE_SET_ID = 4;

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $configurablesCount = $this->fixtureModel->getValue('configurable_products', 0);
        if (!$configurablesCount) {
            return;
        }
        $this->fixtureModel->resetObjectManager();
        $this->generateAttribute('', $this->fixtureModel->getValue('configurable_products_variation', 3));

        /* @var $cache \Magento\Framework\App\CacheInterface */
        $cache = $this->fixtureModel->getObjectManager()->get(\Magento\Framework\App\CacheInterface::class);

        $cacheKey = \Magento\Eav\Model\Config::ATTRIBUTES_CACHE_ID . \Magento\Catalog\Model\Product::ENTITY;
        $cache->remove($cacheKey);
    }

    /**
     * {@inheritdoc}
     */
    public function getActionTitle()
    {
        return 'Generating configurable EAV variations';
    }

    /**
     * {@inheritdoc}
     */
    public function introduceParamLabels()
    {
        return [];
    }

    /**
     * @param int|string $index
     * @param int $optionCount
     * @return void
     */
    private function generateAttribute($index, $optionCount)
    {
        /* @var $model \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $model = $this->fixtureModel->getObjectManager()
            ->create(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
        /** @var \Magento\Store\Model\StoreManager $storeManager */
        $storeManager = $this->fixtureModel->getObjectManager()->create(\Magento\Store\Model\StoreManager::class);
        $stores = $storeManager->getStores();
        $storeViewsCount = count($stores);
        $options = [];

        for ($option = 1; $option <= $optionCount; $option++) {
            $options['order']['option_' . $option] = $option;
            $options['value']['option_' . $option] = array_fill(0, $storeViewsCount + 1, 'option ' . $option);
            $options['delete']['option_' . $option] = '';
        }

        $data = [
            'frontend_label' => array_fill(0, $storeViewsCount + 1, 'configurable variations'),
            'frontend_input' => 'select',
            'is_required' => '0',
            'option' => $options,
            'default' => ['option_0'],
            'attribute_code' => 'configurable_variation' . $index,
            'is_global' => '1',
            'default_value_text' => '',
            'default_value_yesno' => '0',
            'default_value_date' => '',
            'default_value_textarea' => '',
            'is_unique' => '0',
            'is_searchable' => '0',
            'is_visible_in_advanced_search' => '0',
            'is_comparable' => '0',
            'is_filterable' => '0',
            'is_filterable_in_search' => '0',
            'is_used_for_promo_rules' => '0',
            'is_html_allowed_on_front' => '1',
            'is_visible_on_front' => '0',
            'used_in_product_listing' => '0',
            'used_for_sort_by' => '0',
            'source_model' => null,
            'backend_model' => null,
            'apply_to' => [],
            'backend_type' => 'int',
            'entity_type_id' => 4,
            'is_user_defined' => 1,
        ];
        /**
         * The logic is not obvious, but looking to the controller logic for configurable products this attribute
         * requires to be saved twice to become a child of Default attribute set and become available for creating
         * and|or importing configurable products.
         * See MAGETWO-16492
         */
        $model->addData($data);
        $attributeSet = $this->fixtureModel->getObjectManager()->get(\Magento\Eav\Model\Entity\Attribute\Set::class);
        $attributeSet->load(self::ATTRIBUTE_SET_ID);

        $model->setAttributeSetId(self::ATTRIBUTE_SET_ID);
        $model->setAttributeGroupId($attributeSet->getDefaultGroupId(4));
        $model->save();

        $model->setAttributeSetId(self::ATTRIBUTE_SET_ID);
        $model->save();
    }
}
