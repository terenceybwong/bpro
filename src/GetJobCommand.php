<?php

declare(strict_types=1);

namespace AskNicely\BPro;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class GetJobCommand extends AbstractCommand
{
    protected static $defaultName = 'job:fetch';

    protected function configure(): void
    {
        $this->setDescription('Fetch completed jobs')
            ->addOption('export-format', 'x', InputOption::VALUE_REQUIRED, 'Export format (csv|json)', 'csv')
            ->addOption('output-format', 'o', InputOption::VALUE_REQUIRED, 'Output format (csv|json)', 'csv')
            ->addOption('instance', 'i', InputOption::VALUE_REQUIRED, 'Instance to login to', 'instance1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accessToken = (new AccessToken())->getAccessToken($input->getOption('instance'));
        $jobHandler = new Job($accessToken, $this->serializer);
        $eventHandler = new Event($accessToken, $this->serializer);

        $jobs = $jobHandler->getJobs($input->getOption('export-format'));

        foreach ($jobs as &$order) {
            $events = $eventHandler->getEvents($order['Franchise ID'], $order['Order ID']);
            foreach ($events as $event) {
                foreach ($event as $key => $value) {
                    if (\array_key_exists($key, $order) && !empty($order[$key])) {
                        continue;
                    }
                    $order[$key] = $value;
                }
            }
        }
        unset($order);

        print $this->serializer->serialize(
            $jobs,
            $input->getOption('output-format') === 'json' ? JsonEncoder::FORMAT : CsvEncoder::FORMAT
        );
    }
}
