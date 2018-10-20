<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/20
 * Time: 20:06
 */

namespace rabbit\rpcserver;


use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use rabbit\exception\NotSupportedException;
use rabbit\helper\JsonHelper;
use rabbit\web\MessageTrait;
use rabbit\web\Uri;

/**
 * Class Request
 * @package rabbit\rpcserver
 */
class Request implements ServerRequestInterface
{
    use MessageTrait;

    /**
     * @var array
     */
    private $queryParams = [];

    /**
     * @var array
     */
    private $parsedBody = [];

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var UriInterface|Uri
     */
    private $uri;

    public function __construct(array $data)
    {
        $this->uri = self::getUriFromGlobals($data);
        $this->stream = new SwooleStream(JsonHelper::encode($data['params']));

        $this->withQueryParams(isset($data['query']) ? $data['query'] : [])
            ->withParsedBody($data['params']);
    }

    /**
     * @return string|void
     * @throws NotSupportedException
     */
    public function getRequestTarget()
    {
        throw new NotSupportedException("can not call " . __METHOD__);
    }

    /**
     * @param mixed $requestTarget
     * @return void|static
     * @throws NotSupportedException
     */
    public function withRequestTarget($requestTarget)
    {
        throw new NotSupportedException("can not call " . __METHOD__);
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return 'RPC';
    }

    /**
     * @param string $method
     * @return void|static
     * @throws NotSupportedException
     */
    public function withMethod($method)
    {
        throw new NotSupportedException("can not call " . __METHOD__);
    }

    /**
     * @return UriInterface|void
     * @throws NotSupportedException
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param UriInterface $uri
     * @param bool $preserveHost
     * @return $this|static
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        if ($uri === $this->uri) {
            return $this;
        }

        $this->uri = $uri;

        if (!$preserveHost) {
            $this->updateHostFromUri();
        }

        return $this;
    }

    /**
     * @param array $data
     * @return Uri
     */
    private static function getUriFromGlobals(array $data): Uri
    {
        $uri = new Uri();
        $uri = $uri->withScheme('tcp');

        if (isset($data['host'])) {
            $uri->withHost($data['host']);
        }

        if (isset($data['port'])) {
            $uri->withPort($data['port']);
        }

        if (isset($data['route'])) {
            $uri->withPath($data['route']);
        }

        if (isset($data['query'])) {
            $uri->withQuery($data['query']);
        }

        return $uri;
    }

    /**
     *
     */
    private function updateHostFromUri()
    {
        $host = $this->uri->getHost();

        if ($host === '') {
            return;
        }

        if (($port = $this->uri->getPort()) !== null) {
            $host .= ':' . $port;
        }

        $header = 'Host';
        $this->headers = [$header => [$host]] + $this->headers;
    }

    /**
     * @return array|void
     * @throws NotSupportedException
     */
    public function getServerParams()
    {
        throw new NotSupportedException("can not call " . __METHOD__);
    }

    /**
     * @return array|void
     * @throws NotSupportedException
     */
    public function getCookieParams()
    {
        throw new NotSupportedException("can not call " . __METHOD__);
    }

    /**
     * @param array $cookies
     * @return void|static
     * @throws NotSupportedException
     */
    public function withCookieParams(array $cookies)
    {
        throw new NotSupportedException("can not call " . __METHOD__);
    }

    /**
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @param array $query
     * @return $this|static
     */
    public function withQueryParams(array $query)
    {
        $this->queryParams = $query;
        return $this;
    }

    /**
     * @return array|void
     * @throws NotSupportedException
     */
    public function getUploadedFiles()
    {
        throw new NotSupportedException("can not call " . __METHOD__);
    }

    /**
     * @param array $uploadedFiles
     * @return void|static
     * @throws NotSupportedException
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        throw new NotSupportedException("can not call " . __METHOD__);
    }

    /**
     * @return array|null|object
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * @param array|null|object $data
     * @return $this|static
     */
    public function withParsedBody($data)
    {
        $this->parsedBody = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public function getAttribute($name, $default = null)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this|static
     */
    public function withAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    public function withoutAttribute($name)
    {
        if (false === array_key_exists($name, $this->attributes)) {
            return $this;
        }

        unset($this->attributes[$name]);

        return $this;
    }

}