<?php
class Router
{
    private $request;

    private $uris = [];

    private $name;

    private $uri;

    private $callable;

    public $data;

    private static $instance;

    public function __construct()
    {
        $this->request = $_GET;
    }

    public function name($name)
    {
        $this->name = $name;

        return $this;
    }

    public function action($callable)
    {
        $this->callable = $callable;

        return $this;
    }

    public function uri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @throws Exception if the uri or name of the route is missing
    */
    public function register()
    {
        if(!isset($this->uri, $this->name)){
            return new Exception("The url or name are missing for a route: {$this->uri} || {$this->name}");
        }

        $name = str_ireplace("-{0}", "", $this->name);
        $this->uris[$name]['controller'] = $this->callable;
//        $this->uris[$name]['parameter'] = $this->hasParameters();

        $this->resetRouteInfo();
    }

    private function getCleanUri()
    {
        $a = explode('-', $this->url());
        $items = [];
        foreach($a as $x)
        {
            if(!is_numeric($x))
            {
                $items[] = $x;
            }
            else
            {
                $url = substr($this->url(), 0, strrpos($this->url(), '-'));
                $this->uris[$url]['parameter'] = $x;
            }
        }

        return implode('-', $items);
    }

    private function hasParameters()
    {
        $parts = explode('-', $this->name);

        foreach($parts as $p)
        {
            if(ctype_digit($p))
                return true;
        }

        return false;
    }

    private function resetRouteInfo()
    {
        $this->uri = null;
        $this->callable = null;
        $this->redirect = null;
    }

    /**
     * @param $name
     * @return bool
     */
    private function uriIsValid($name)
    {
        $a = explode('-', $name);
        $items = [];

        if(!empty($a))
        {
            // Check whether we're dealing with an empty uri,
            // if we do we cannot check for parameters as we know we are dealing with the default uri
            foreach($a as $x)
            {
                if(!is_numeric($x))
                {
                    $items[] = $x;
                }
            }
        }

        $name = implode('-', $items);

        return $this->exist($name);
    }

    /**
     * @return string
    */
    public function url()
    {
//        if(isset($this->request['q']) && empty($this->request['q']))
//            $this->request['q'] = '/';

        return $this->request['q'];
    }

    /**
     *
     * @param $uri string
     * @throws Exception if the file represented by the url is not present
     */
    public function get($uri)
    {
        if($this->uriIsValid($uri))
        {
            return $this->prepareContent();
        }

        // Alternative if everything fails
        throw new Exception("$uri is not a registered route or is there any route that looks like it.");
    }

    public function currentRouteInfo()
    {
        return $this->uris[$this->getCleanUri()];
    }

    public function exist($route)
    {
        return array_key_exists($route, $this->uris);
    }

    public function getCurrentRoute()
    {
        $this->get($this->url());
    }

    protected function prepareContent()
    {
        $data = $this->currentRouteInfo();

        $controller = substr($data['controller'], 0, stripos($data['controller'], '@'));
        $action = substr($data['controller'], stripos($data['controller'], '@')+ 1, strlen($data['controller']));

        $controller = new $controller();

        $body = file_get_contents('php://input');
        $body = json_decode($body, true);

        if(isset($data['parameter']))
        {
            if(!is_null($body)){
                echo json_encode($controller->$action($data['parameter'], $body));
            }
            else{
                echo json_encode($controller->$action($data['parameter']));
            }
        }
        else{
            if(!is_null($body)){
                echo json_encode($controller->$action($body));
            }
            else{
                echo json_encode($controller->$action());
            }
        }
    }
}
