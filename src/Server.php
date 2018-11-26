<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 11:16
 */

namespace rabbit\rpcserver;

use rabbit\core\ObjectFactory;
use rabbit\server\AbstractTcpServer;
use rabbit\socket\tcp\TcpParserInterface;

/**
 * Class Server
 * @package rabbit\rpcserver
 */
class Server extends AbstractTcpServer
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
     * @var string
     */
    private $request;

    /**
     * @var string
     */
    private $response;

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