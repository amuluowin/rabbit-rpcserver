<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/20
 * Time: 23:44
 */

namespace rabbit\rpcserver;


use rabbit\core\ObjectFactory;
use rabbit\parser\ParserInterface;

class RpcParser implements ParserInterface
{
    /**
     * @var int
     */
    private $packageLen;

    /**
     * @var int
     */
    private $packageOffset;

    /**
     * @var string
     */
    private $packageType;

    /**
     * @var ParserInterface
     */
    private $parser;

    public function __construct()
    {
        $this->packageLen = ObjectFactory::get('rpc.setting')['package_length_type_len'];
        $this->packageOffset = ObjectFactory::get('rpc.setting')['package_length_offset'];
        $this->packageType = ObjectFactory::get('rpc.setting')['package_length_type'];
    }

    /**
     * @param mixed $data
     * @return string
     */
    public function encode($data): string
    {
        $data = $this->parser->encode($data);
        $total_length = $this->packageLen + strlen($data) - $this->packageOffset;
        return pack($this->packageType, $total_length) . $data;
    }

    public function decode(string $data)
    {
        $data = substr($data, $this->packageType);
        return $this->parser->decode($data);
    }

}