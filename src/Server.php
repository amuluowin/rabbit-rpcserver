<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 11:16
 */

namespace rabbit\rpcserver;

use rabbit\core\ObjectFactory;
use rabbit\socket\tcp\TcpParserInterface;

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

    /**
     * @var string
     */
    private $request;

    /**
     * @var string
     */
    private $response;

    /**
     * @return \Swoole\Server
     */
    protected function createServer(): \Swoole\Server
    {
        return new \Swoole\Server($this->host, $this->port, $this->type);
    }

    /**
     * @param \Swoole\Server|null $server
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function startServer(\Swoole\Server $server = null): void
    {
        parent::startServer($server);
        $server->on('Receive', array($this, 'onReceive'));
        $server->set(ObjectFactory::get('server.setting'));
        $server->start();
    }

    /**
     * @param \Swoole\Server $server
     * @param int $fd
     * @param int $reactor_id
     * @param string $data
     * @throws \Exception
     */
    public function onReceive(\Swoole\Server $server, int $fd, int $reactor_id, string $data): void
    {
        /**
         * @var TcpParserInterface $parser
         */
        $parser = ObjectFactory::get('rpc.parser');
        $data = $parser->decode($data);
        $psrRequest = $this->request['class'];
        $psrResponse = $this->response['class'];
        $this->dispatcher->dispatch(new $psrRequest($data, $fd), new $psrResponse($server, $fd));
    }

}