<?php
namespace PMVC\PlugIn\view_config_helper;

// \PMVC\l(__DIR__.'/xxx.php');

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\view_config_helper';

class view_config_helper extends \PMVC\PlugIn
{
    public function init()
    {
        \PMVC\callPlugin(
            'dispatcher',
            'attach',
            array(
                $this,
                \PMVC\Event\B4_PROCESS_VIEW 
            )
        );
    }
    
   public function onB4ProcessView() {
        $dot = \PMVC\plug('dotenv');
        $view = \PMVC\plug('view');
        $viewConfigs = $dot->getArray('.env.view');
        $view->set($viewConfigs);
   }
}
