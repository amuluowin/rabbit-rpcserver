<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 11:15
 */

namespace rabbit\rpcserver;

use rabbit\core\ObjectFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RpcCommand
 * @package rabbit\rpcserver
 */
class RpcCommand extends Command
{
    /**
     *
     */
    protected function configure(): void
    {
        $this->setName('rpc:server')->setDescription('start|stop|reload httpserver')->setHelp('This command allows you to start|stop|reload httpserver.')
            ->addArgument('cmd', InputArgument::REQUIRED, 'start|stop|reload');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $cmd = $input->getArgument('cmd');
        ObjectFactory::get('rpcserver')->$cmd();
    }
}