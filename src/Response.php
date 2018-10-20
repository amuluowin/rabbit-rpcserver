<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/20
 * Time: 22:48
 */

namespace rabbit\rpcserver;


use Psr\Http\Message\ResponseInterface;
use rabbit\exception\NotSupportedException;
use rabbit\web\MessageTrait;
use rabbit\web\SwooleStream;

/**
 * Class Response
 * @package rabbit\rpcserver
 */
class Response implements ResponseInterface
{
    use MessageTrait;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var int
     */
    private $fd;

    /**
     * @var \Swoole\Server
     */
    private $server;

    public function __construct(\Swoole\Server $server, int $fd)
    {
        $this->server = $server;
        $this->fd = $fd;
    }

    public function getStatusCode()
    {
        throw new NotSupportedException("can not call " . __METHOD__);
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        throw new NotSupportedException("can not call " . __METHOD__);
    }

    public function getReasonPhrase()
    {
        throw new NotSupportedException("can not call " . __METHOD__);
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function getAttribute($name, $default = null)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }

    /**
     * @param $name
     * @param $value
     * @return Response
     */
    public function withAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * @param $content
     * @return Response
     */
    public function withContent($content): Response
    {
        if ($this->stream) {
            return $this;
        }

        $this->stream = new SwooleStream($content);
        return $this;
    }

    /**
     * 处理 Response 并发送数据
     */
    public function send()
    {
        $this->server->send($this->getBody()->getContents());
    }

}