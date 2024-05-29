<?php
namespace MageWorx\CountdownTimersBase\Console\Command\AssignByConditionCommand;

/**
 * Interceptor class for @see \MageWorx\CountdownTimersBase\Console\Command\AssignByConditionCommand
 */
class Interceptor extends \MageWorx\CountdownTimersBase\Console\Command\AssignByConditionCommand implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Event\ManagerInterface $eventManager, \Psr\Log\LoggerInterface $logger, ?string $name = null)
    {
        $this->___init();
        parent::__construct($eventManager, $logger, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function run(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'run');
        return $pluginInfo ? $this->___callPlugins('run', func_get_args(), $pluginInfo) : parent::run($input, $output);
    }
}
