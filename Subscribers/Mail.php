<?php

namespace K10rStaging\Subscribers;

use Enlight\Event\SubscriberInterface;
use Enlight_Components_Mail;
use K10rStaging\Services\ConfigService;

class Mail implements SubscriberInterface
{
    const MAILTRAP_HOST = 'smtp.mailtrap.io';
    const MAILTRAP_PORT = 465;
    const MAILTRAP_AUTH = 'login';

    /** @var ConfigService */
    private $configReader;

    public function __construct(ConfigService $configReader)
    {
        $this->configReader = $configReader;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Components_Mail_Send' => 'onSendMail',
        ];
    }

    public function onSendMail()
    {
        $config = $this->configReader->getConfig();

        $mailTrapOptions = [
            'host'     => self::MAILTRAP_HOST,
            'port'     => self::MAILTRAP_PORT,
            'auth'     => self::MAILTRAP_AUTH,
            'username' => $config['mailtrapUsername'],
            'password' => $config['mailtrapPassword'],
        ];

        if (!empty($mailTrapOptions['username']) && !empty($mailTrapOptions['password'])) {
            $transport = \Enlight_Class::Instance('Zend_Mail_Transport_Smtp', [$mailTrapOptions['host'], $mailTrapOptions]);
            Enlight_Components_Mail::setDefaultTransport($transport);
        }
    }
}
