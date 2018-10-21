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
use rabbit\parser\ParserInterface;
use rabbit\web\NotFoundHttpException;

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
        $route = explode('/', ltrim($request->getUri()->getPath(), '/'));
        if (count($route) < 2) {
            throw new NotFoundHttpException("can not find the route:$route");
        }
        $controller = 'apis';
        foreach ($route as $index => $value) {
            if ($index === count($route) - 1) {
                $action = $value;
            } elseif ($index === count($route) - 2) {
                $controller .= '\controllers\\' . ucfirst($value) . 'Controller';
            } else {
                $controller .= '\\' . $value;
            }
        }
        $controller = ObjectFactory::get($controller);
        /**
         * @var ResponseInterface $response
         */
        $response = call_user_func_array([$controller, $action], $request->getQueryParams());

        if (!$response instanceof ResponseInterface) {
            /**
             * @var ResponseInterface $newResponse
             * @var ParserInterface $parser
             */
            $newResponse = Context::get('response');
            $parser = ObjectFactory::get('rpc.parser');
            $response = $parser->encode($response);
            $response = $newResponse->withContent($response);
        }

        return $response;
    }
}