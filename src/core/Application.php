<?php

namespace Untitled\Core;

class Application
{
    const ENV_DEVELOPMENT = 'development';
    const ENV_PRODUCTION = 'production';
    
    private static $instance;

    private $environment;
    private $injector;
    private $routeInfo;
    private $logger;
    private $configurations;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if ( ! isset( self::$instance ) ) 
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the value of environment
     */ 
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Set the value of environment
     *
     * @return  self
     */ 
    public function setEnvironment($environment)
    {
        $this->environment = $environment;

        return $this;
    }


    /**
     * Get the value of injector
     */ 
    public function getInjector()
    {
        return $this->injector;
    }

    /**
     * Set the value of injector
     *
     * @return  self
     */ 
    public function setInjector($injector)
    {
        $this->injector = $injector;

        return $this;
    }

    /**
     * Get the value of routeInfo
     */ 
    public function getRouteInfo()
    {
        return $this->routeInfo;
    }

    /**
     * Set the value of routeInfo
     *
     * @return  self
     */ 
    public function setRouteInfo($routeInfo)
    {
        $this->routeInfo = $routeInfo;

        return $this;
    }

    /**
     * Get the value of logger
     */ 
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Set the value of logger
     *
     * @return  self
     */ 
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Get the value of configurations
     */ 
    public function getConfigurations()
    {
        return $this->configurations;
    }

    /**
     * Set the value of configurations
     *
     * @return  self
     */ 
    public function setConfigurations($configurations)
    {
        $this->configurations = $configurations;

        return $this;
    }
}
