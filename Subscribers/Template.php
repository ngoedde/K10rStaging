<?php

namespace K10rStaging\Subscribers;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Plugins_ViewRenderer_Bootstrap as ViewRenderer;
use Enlight_Event_EventArgs as EventArgs;
use Enlight_Template_Manager;
use K10rStaging\Services\ConfigService;

class Template implements SubscriberInterface
{
    /** @var ConfigService */
    private $configService;

    /** @var string */
    private $pluginDir;
    /** @var Enlight_Template_Manager */
    private $template;

    public function __construct(ConfigService $configService, Enlight_Template_Manager $template, $pluginDir)
    {
        $this->configService = $configService;
        $this->pluginDir     = $pluginDir;
        $this->template      = $template;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Theme_Inheritance_Template_Directories_Collected' => 'onCollectTemplateDir',
            'Enlight_Plugins_ViewRenderer_FilterRender'        => 'onFilterRenderer',
        ];
    }

    public function onFilterRenderer(EventArgs $args)
    {
        /** @var ViewRenderer $viewRenderer */
        $viewRenderer = $args->get('subject');
        $module       = strtolower($viewRenderer->Front()->Request()->getModuleName());

        if ($module !== 'backend') {
            return;
        }

        $stagingBadge = $this->template->fetch($this->pluginDir . '/Resources/views/backend/k10r_staging/badge.tpl');

        $args->setReturn(
            str_replace(
                '</body>',
                $stagingBadge . '</body>',
                $args->getReturn()
            )
        );
    }

    public function onCollectTemplateDir(EventArgs $args)
    {
        $directories = $args->getReturn();

        if (!$this->configService->getConfig()['stagingNotice']) {
            return $directories;
        }

        $directories[] = $this->pluginDir . '/Resources/views';

        return $directories;
    }
}
