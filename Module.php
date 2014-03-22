<?php
namespace EdpModuleLayouts;

use Zend\Mvc\MvcEvent, Zend\Mvc\Application;

class Module
{
    public function onBootstrap($e)
    {
        $config       = $e->getApplication()->getServiceManager()->get('config');
        $eventManager = $e->getApplication()->getEventManager();
        
        $eventManager->attach(
            MvcEvent::EVENT_DISPATCH_ERROR, 
            function(\Zend\Mvc\MvcEvent $e) use($config) {
                if($e->getError() === Application::ERROR_ROUTER_NO_MATCH) {
                    if(isset($config['module_layouts']['NotFound'])){
                        $e->getViewModel()->setTemplate($config['module_layouts']['NotFound']);
                    }
                }          
            }, 
            100
        );
                
        $eventManager->getSharedManager()->attach('Zend\Mvc\Controller\AbstractController', 'dispatch', function($e) use($config) {
            $controller      = $e->getTarget();
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            
            if (isset($config['module_layouts'][$moduleNamespace])) {
                $controller->layout($config['module_layouts'][$moduleNamespace]);
            }
        }, 100);
    }
}
