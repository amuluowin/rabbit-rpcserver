<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 11:16
 */

namespace rabbit\rpcserver;

use rabbit\core\ObjectFactory;
use rabbit\rpcclient\parser\TcpParserInterface;

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

    public function onReceive(\Swoole\Server $server, int $fd, int $reactor_id, string $data): void
    {
        /**
         * @var TcpParserInterface $parser
         */
        $parser = ObjectFactory::get('rpc.parser');
        $data = $parser->decode($data);
        $psrRequest = $this->request['class'];
        $psrResponse = $this->response['class'];
        $this->dispatcher->dispatch(new $psrRequest($data), new $psrResponse($server, $fd));
    }

}