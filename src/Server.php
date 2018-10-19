<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 11:16
 */

namespace rabbit\rpcserver;

/**
 * Class Server
 * @package rabbit\rpcserver
 */
class Server extends \rabbit\server\Server
{
    /**
     * @var string
     */
    protected $host = '0.0.0.0';

    /**
     * @var int
     */
    protected $port = 9503;

    /**
     * @var int
     */
    protected $type = SWOOLE_PROCESS;

    protected function createServer(): \Swoole\Server
    {
        return new swoole_server($this->host, $this->port, $this->type);
    }

    protected function startServer(\Swoole\Server $server = null): void
    {
        parent::startServer($server);
        $server->on('Receive', array($this, 'onReceive'));
        $server->set(ObjectFactory::get('server.setting'));
        $server->start();
    }

}