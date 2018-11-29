<?php

namespace K10rStaging\Services;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadService
{
    /** @var OutputInterface */
    private $outputInterface;

    public function download($url, $outputPath, OutputInterface $outputInterface = null)
    {
        $this->outputInterface = $outputInterface;

        $this->outputInterface->writeln(sprintf('Downloading dump to %s', $outputPath));

        $this->downloadFile($url, $outputPath);
    }

    private function downloadFile($url, $path)
    {
        /** @var $inputFile */
        $inputFile = fopen($url, 'rb');

        if ($inputFile === false) {
            $this->outputInterface->writeln('<error>Failed to open file!</error>');
        }

        $inputSize = (int) array_change_key_case(get_headers($url, 1))['content-length'];

        if ($inputSize === 0) {
            $this->outputInterface->writeln('<comment>Warning: Did not receive content-length header (unknown remote file size)</comment>');
            $progressBarFormat = '%current% MB/unknown [%bar%] unknown% %elapsed:6s%';
        } else {
            $this->outputInterface->writeln(sprintf('Download size: <info>%s</info>', $inputSize));
            $progressBarFormat = '%current% MB/%max% MB [%bar%] %percent:3s%% %elapsed:6s%';
        }

        //Actual download starts here
        if ($inputFile) {
            $outputFile = fopen($path, 'wb');

            if ($outputFile) {
                $chunkSize = 1024 * 1024;
                $progressBar = new ProgressBar($this->outputInterface);
                $progressBar->setFormat($progressBarFormat);

                $progressBar->start($inputSize / 1024 / 1024); //Always use MB

                while(feof($inputFile) === false) {
                    fwrite($outputFile, fread($inputFile, $chunkSize), $chunkSize);

                    $downloadProgress = ftell($inputFile) / 1024 / 1024;

                    $progressBar->setProgress($downloadProgress);
                }

                $progressBar->finish();
            }
        }

        if ($inputFile) {
            fclose($inputFile);
        }

        if ($outputFile) {
            fclose($outputFile);
        }
    }
}
