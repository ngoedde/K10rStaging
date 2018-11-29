<?php

namespace K10rStaging\Commands;

use Doctrine\DBAL\Tools\Console\Command\ImportCommand;
use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportDump extends ShopwareCommand
{

    public function configure()
    {
        $this->setDescription('Import the sql dump from the URL specified in the plugin config or provided by an argument.')->addArgument(
                'url',
                InputArgument::OPTIONAL,
                'The URL to the database dump'
             )->setHelp(
            'The <info>%command.name%</info> imports the dump from a specified URL. If no URL was specified, the URL from the plugin config will be taken.'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');

        if (empty($url)) {
            $configService = $this->container->get('k10r_staging.services.config');

            $url = $configService->getConfig()['dumpUrl'];
        }

        $output->writeln(sprintf('Importing database dump from <info>%s</info>', $url));

        $path = sprintf('%s/files/dump.sql', $this->container->getParameter('kernel.root_dir'));
        $downloadService = $this->container->get('k10r_staging.services.download');
        $downloadService->download($url, $path, $output);

        $output->writeln('');
        $output->writeln('<info>Importing dump...</info>');

        /** @var ImportCommand $command */
        $command = $this->getApplication()->find('dbal:import');
        $result = $command->run(new ArrayInput(['file' => $path]), $output);

        if ($result === 0) {
            $output->writeln('<info>Database update complete!</info>');
        }

        unlink($path);
    }
}
