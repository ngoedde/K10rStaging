<?php

namespace K10rStaging;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class K10rStaging extends Plugin
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('k10r_staging.plugin_dir', $this->getPath());

        parent::build($container);
    }

    /**
     * {@inheritdoc}
     */
    public function install(InstallContext $context)
    {
        $this->applyUpdates(null, $context->getCurrentVersion());

        parent::install($context);
    }

    /**
     * {@inheritdoc}
     */
    public function update(UpdateContext $updateContext)
    {
        $this->applyUpdates($updateContext->getCurrentVersion(), $updateContext->getUpdateVersion());

        parent::update($updateContext);
    }

    /**
     * @param null|string $oldVersion
     * @param null|string $newVersion
     *
     * @return bool
     */
    private function applyUpdates($oldVersion = null, $newVersion = null)
    {
        $versionClosures = [
            '1.0.0' => function () {
                return true;
            },
        ];

        foreach ($versionClosures as $version => $versionClosure) {
            if ($oldVersion === null || (version_compare($oldVersion, $version, '<') && version_compare($version, $newVersion, '<='))) {
                if (!$versionClosure($this)) {
                    return false;
                }
            }
        }

        return true;
    }
}
