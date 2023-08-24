<?php
namespace REST;

Class Main
{
    private static $event;

    private static $context;

    private static $headers;

    /**
     * @param $event
     * @param $context
     */
    public function __construct($event, $context)
    {
        self::$event    = $event;
        self::$context  = $context;
        self::$headers  = $event->headers;
    }

    /**
     * 运行
     * @return bool|mixed|string
     */
    public static function run()
    {
        //路由初始化
        $route = new Route(self::$event);
        list ($item, $controller, $function, $params, $body) = $route->init();
        if (!$item) {
            return [
                'code'      => 404,
                'err_code'  => 10000,
                'msg'       => '资源不存在'
            ];
        }

        if (class_exists($controller)) {
            $c = new $controller();
            if (method_exists($c, $function)) {
                return $c->$function();
            }
        }

        //类或函数不存在
        return [
            'code'      => 404,
            'err_code'  => 10000,
            'msg'       => '资源不存在'
        ];
    }

}