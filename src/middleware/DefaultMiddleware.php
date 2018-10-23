<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/22
 * Time: 0:45
 */

namespace rabbit\rpcserver\middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use rabbit\core\Context;
use rabbit\core\ObjectFactory;
use rabbit\server\AttributeEnum;

/**
 * Class DefaultMiddleware
 * @package rabbit\rpcserver\middleware
 */
class DefaultMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute(AttributeEnum::ROUTER_ATTRIBUTE);
        list($service, $method) = explode('::', $route);
        $serviceList = ObjectFactory::get('rpc.services');
        if (!array_key_exists($service, $serviceList)) {
            throw new \BadMethodCallException('can not call ' . strpos($service, '/') === 0 ? $service . '/' . $method : $service . '->' . $method);
        }

        $controller = ObjectFactory::get($serviceList[$service], null, false);
        if ($controller === null) {
            throw new \BadMethodCallException('can not call ' . strpos($service, '/') === 0 ? $service . '/' . $method : $service . '->' . $method);
        }
        /**
         * @var ResponseInterface $response
         */
        $response = call_user_func_array([$controller, $method], $request->getQueryParams());

        if (!$response instanceof ResponseInterface) {
            /**
             * @var ResponseInterface $newResponse
             */
            $newResponse = Context::get('response');
            $response = $newResponse->withContent($response);
        }

        return $response;
    }
}