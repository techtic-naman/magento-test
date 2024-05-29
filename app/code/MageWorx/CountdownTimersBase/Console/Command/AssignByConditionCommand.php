<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Console\Command;

use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

class AssignByConditionCommand extends Command
{
    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AssignByConditionCommand constructor.
     *
     * @param EventManagerInterface $eventManager
     * @param LoggerInterface $logger
     * @param string|null $name
     */
    public function __construct(
        EventManagerInterface $eventManager,
        LoggerInterface $logger,
        string $name = null
    ) {
        parent::__construct($name);
        $this->eventManager = $eventManager;
        $this->logger       = $logger;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('countdowntimers:product:assign-by-condition');
        $this->setDescription('Assign products by condition');

        parent::configure();
    }

    /**
     * @return bool
     */
    protected function isEnable(): bool
    {
        return true;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $this->eventManager->dispatch('mageworx_countdowntimersbase_assign_by_cron');
            $output->writeln('The process has been finished successfully.');
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $output->writeln($e->getMessage());
        }
    }
}