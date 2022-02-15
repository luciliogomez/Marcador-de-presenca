<?php
namespace App\Http;

use App\Http\Middlewares\Queue as  MiddlewareQueue;
use App\Utils\View;
use Closure;
use Exception;
use ReflectionFunction;

class Router{

    private $url;

    private $prefix;

    private $routes = [];

    private $request;

    public function __construct($url)
    {
        $this->url = $url;
        $this->request = new Request($this);
        $this->setPrefix();
    }

    public function getRequest()
    {
        return $this->request;
    }
    public function setPrefix()
    {
        $parseUrl = parse_url($this->url);
        $prefix = $parseUrl['path'];

        $this->prefix = $prefix;
    }

    public function get($route,$params = [])
    {
        $this->addRoute("GET",$route,$params);
    }

    public function post($route,$params = [])
    {
        $this->addRoute("POST",$route,$params);
    }

    public function addRoute($method,$route,$params =[ ])
    {
        foreach($params as $key => $value)
        {
            if($value instanceof Closure)
            {
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }

        $params['variables'] = [];

        $patternVariable = "-{(.*?)}-";
        if(preg_match_all($patternVariable,$route,$matches)){
            $route = preg_replace($patternVariable,'(.*?)',$route);
            $params['variables'] = $matches[1];    

        }

        $patternRoute = "/^". str_replace("/","\/",$route) ."$/";
        $this->routes[$patternRoute][$method] = $params;
    
        
    }

    public function run()
    {
        try{
            $route = $this->getActualRoute();
            if(!isset($route['controller']))
            {
                throw new Exception("ERRO NO SERVIDOR",500);
            }

            $args = [];
            
            $reflection = new ReflectionFunction($route['controller']);
            foreach($reflection->getParameters() as $parameter){
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name];
            }

            if(!isset($route['middlewares']))
            {
                $route['middlewares'] = [];
            }
            
            $route['middlewares'] = array_merge(MiddlewareQueue::$default,$route['middlewares']);
            
            
            return (new MiddlewareQueue($route['middlewares'],$route['controller'],$args))->next($this->request);


            // return call_user_func_array($route['controller'],$args);

        }catch(Exception $ex)
        {
            return new Response($ex->getCode(),View::render('layout/error',[
                'code' => $ex->getCode(),
                'errorMessage' => 'PAGINA NÃO ENCONTRADA'  
            ]));
        }
    }

    public function getActualRoute()
    {
        $uri = $this->getUri();
        $httpMethod = $this->request->getHttpMethod();

        foreach($this->routes as $patternRoute => $methods)
        {

            if(preg_match($patternRoute,$uri,$matches)){

                if(isset($methods[$httpMethod])){
                    unset($matches[0]);
                    
                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys,$matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;

                    return $methods[$httpMethod];
                }
                throw new Exception("METODO NAO DEFINIDO",405);
            }
        }
        throw new Exception("URL INDEFINIDA",404);
        
    }

    public function getUri()
    {
        $uri = $this->request->getUri();

        $prefix = (strlen($this->prefix))?explode($this->prefix,$uri):[$uri];
        return end($prefix);
    }


    public function redirect($route)
    {
        $url = $this->url.$route;
        
        header('Location: '.$url);
        exit;
    }
}
