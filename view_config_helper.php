<?php
namespace PMVC\PlugIn\view_config_helper;

// \PMVC\l(__DIR__.'/xxx.php');

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\view_config_helper';

\PMVC\initPlugIn(['controller'=>null]);

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
        $dotView = '.env.view';
        if ($dot->fileExists($dotView)) { 
            $viewConfigs = $dot->getArray($dotView);
            $view->set($viewConfigs);
        }
        $globalView = \PMVC\getOption('VIEW');
        if ($globalView) {
            $view->set($globalView);
        }
        $i18n = \PMVC\getOption('I18N');
        if ($i18n) {
            $view->set($i18n);
        }
   }
}
