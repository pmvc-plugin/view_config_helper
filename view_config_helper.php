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
            [
                $this,
                \PMVC\Event\B4_PROCESS_VIEW 
            ]
        );
    }
    
   public function onB4ProcessView() {
        $dot = \PMVC\plug('dotenv');
        $view = \PMVC\plug('view');
        $dotView = '.env.view';
        if ($dot->fileExists($dotView)) { 
            $configs = $dot->getUnderscoreToArray($dotView);
        } else { 
            $configs = [];
        }
        $globalView = \PMVC\getOption('VIEW');
        if ($globalView) {
            $configs = array_replace_recursive($configs, $globalView);
            \PMVC\option('set', 'VIEW', null);
        }
        $i18n = \PMVC\getOption('I18N',[]);
        $configs = array_replace_recursive($configs, ['I18N'=>$i18n]);
        \PMVC\option('set', 'I18N', null);
        $view->set($configs);
   }
}
