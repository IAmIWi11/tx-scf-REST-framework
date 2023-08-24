<?php
namespace REST;

class Route {
    public $path;
    public $method;
    public $params;
    public $body;
    public $routes;
    public $item;

    public function __construct($event)
    {
        //去除版本号
        $pattern = '/\/v\d+\//';
        $replacement = '/';
        $event->path = preg_replace($pattern, $replacement, $event->path);

        $this->path     = $event->path ?: $event->Message;
        $this->method   = $event->httpMethod;
        $this->params   = json_decode(json_encode($event->queryString), true);//获取param参数
        $this->body     = json_decode($event->body, true);//获取post参数
        require 'routes.php';
        $this->routes   = $routes;
    }

    /**
     * 路由初始化
     * @return array|bool
     */
    public function init() {
        //路径
        $path       = substr(strstr(substr($this->path, 1), '/'), 0) ?: '/';
        var_dump($path);

        foreach ($this->routes[$this->method] as $pattern => $functionStr) {

            //替换'/'为'\/'，并获取参数名
            $pattern = str_replace('/', '\/', $pattern);
            preg_match_all('/\{([a-zA-Z]+)\}/', $pattern, $matches);
            foreach ($matches[1] as $k => $v) {
                $paramsKey[] = $v;
            }

            //替换参数为正则表达式
            $pattern = preg_replace('/\{[a-zA-Z]+\}/', '([a-zA-Z0-9-_]+)', $pattern);

            //匹配路由
            if (preg_match("/^{$pattern}$/", $path, $matches)) {

                //设置参数
                if ($paramsKey) {
                    foreach ($paramsKey as $k => $v) {
                        $params[$v] = $matches[$k + 1];
                    }
                    $this->params = array_merge($this->params, $params);
                }
                $functionArr = explode('/', $functionStr);
                break;
            }
        }

        // 未定义的路由
        if (!$functionArr) {
            return false;
        }

        return [
            true,
            $this->camelCase($this->unCamelCase($functionArr[0])),
            lcfirst($this->camelCase($this->unCamelCase($functionArr[1]))),
            $this->params,
            $this->body,
        ];
    }

    /**
     * 下划线转驼峰
     *
     * @param string $string    原字符
     * @param string $separator 分隔符
     *
     * @return string
     */
    private function camelCase(string $string, string $separator = '-')
    {
        $string = $separator . str_replace($separator, ' ', strtolower($string));
        return str_replace(' ', '', ucwords(ltrim($string, $separator)));
    }

    /**
     * 驼峰转下划线
     *
     * @param string $camelCaps
     * @param string $separator
     *
     * @return string
     */
    private function unCamelCase(string $camelCaps, string $separator = '-')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}