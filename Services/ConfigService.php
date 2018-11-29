<?php

namespace K10rStaging\Services;

use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\CachedConfigReader;
use Shopware\Models\Shop\Shop;

class ConfigService
{
    /** @var CachedConfigReader */
    private $configReader;

    /** @var Shop */
    private $shop;

    /** @var ModelManager */
    private $models;

    /** @var ContextService */
    private $contextService;

    public function __construct(CachedConfigReader $configReader, ContextService $contextService, ModelManager $models)
    {
        $this->configReader   = $configReader;
        $this->models         = $models;
        $this->contextService = $contextService;
    }

    public function getConfig()
    {
        if ($this->shop === null) {
            $shopId = $this->contextService->getShopContext()->getShop()->getId();

            $this->shop = $this->models->getRepository(Shop::class)->find($shopId);
        }

        return $this->configReader->getByPluginName('K10rStaging', $this->shop);
    }
}
