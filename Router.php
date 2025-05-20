<?php
declare(strict_types=1);

class Router{

    public $routes;

    public function __construct()
    {
        $this->routes = [
            "GET" => [],
            "POST" => [],
            "PUT" => [],
            "PATCH" => [],
            "DELETE" => []
        ];
    }

    //a partir de las configuraciones de un endpoint como: uri, method, controller y action
    //se pueden definir rutas que acepten query parameters, route parameters o sin estos.
    public function add(array $endpoint) : void {
        $uriParts = preg_split("/[\/?]/", $endpoint["uri"]); //se desgloza la uri en partes a partir de / o ? 
        
        $uriParam = "";

        //se verifica si esta presente el parametro de la ruta, y este se reconoce como query o route parameter.
        if(isset($uriParts[2])){ 
            $uriParam = str_contains($uriParts[2], '=') ? "query" : "{id}";
        }

        //validacion de los tipos de metodos que se pueden definir
        if(array_key_exists($endpoint["method"], $this->routes)){
            $actionPath = fullPath("controllers/{$endpoint['controller']}/{$endpoint['action']}.php");

            //validacion de la existencia de la ruta del archivo y asignacion de la nueva ruta a routes.
            if(isset($actionPath)){ 
                $this->routes[$endpoint["method"]][$endpoint["controller"]][$uriParam]["actionPath"] = $actionPath;
            }
            else{
                echo "Ha ocurrido un error con la ruta de la accion";
            }
        }
        else{
            echo "Ha ocurrido un error con el metodo de la solicitud";
        }
    }

    public function run(string $method, string $uri) : void {
        $uriParts = preg_split("/[\/?]/", $uri); //se desgloza la uri en partes a partir de / o ? 

        //manejar rutas vacias: "/"
        $uriController = $uriParts[1] === "" ? "home" : $uriParts[1]; 
        
        $uriParameter = "";

        if(isset($uriParts[2])){
            $uriParameter = str_contains($uriParts[2], '=') ? "query" : "{id}";
        }

        $actionPathExists = isset($this->routes[$method][$uriController][$uriParameter]["actionPath"]);

        if($actionPathExists){
            require $this->routes[$method][$uriController][$uriParameter]["actionPath"];
        }
        else{
            echo "Page not found";
        }
    }

    public function get(array $endpointInfo): void{
        $endpointInfo["method"] = "GET";
        $this->add($endpointInfo);
    }

    public function post(array $endpointInfo): void{
        $endpointInfo["method"] = "POST";
        $this->add($endpointInfo);
    }

    public function put(array $endpointInfo): void{
        $endpointInfo["method"] = "PUT";
        $this->add($endpointInfo);
    }

    public function patch(array $endpointInfo): void{
        $endpointInfo["method"] = "PATCH";
        $this->add($endpointInfo);
    }

    public function delete(array $endpointInfo): void{
        $endpointInfo["method"] = "DELETE";
        $this->add($endpointInfo);
    }
}