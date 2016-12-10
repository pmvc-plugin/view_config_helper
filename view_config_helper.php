<?php
namespace PMVC\PlugIn\view_config_helper;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\view_config_helper';

\PMVC\initPlugIn(['controller'=>null]);

/**
 * @parameters funciton callback
 * @parameters funciton .env
 */
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
    
   public function onB4ProcessView()
   {
        $dot = \PMVC\plug('dotenv');
        $view = \PMVC\plug('view');
        $configs = [];
        $globalView = \PMVC\getOption('VIEW');
        if ($globalView) {
            $configs = array_replace_recursive($configs, $globalView);
            \PMVC\option('set', 'VIEW', null);
        }
        $i18n = \PMVC\getOption('I18N');
        if ($i18n) {
            $configs = array_replace_recursive($configs, ['I18N'=>$i18n]);
            \PMVC\option('set', 'I18N', null);
        }
        $dotView = \PMVC\value($this,['.env'],'.env.view');
        if ($dot->fileExists($dotView)) {
            $configs = array_replace_recursive(
                $configs,
                $dot->getUnderscoreToArray($dotView)
            );
        }
        if ($this['callback']) {
            $this['callback']($configs);
        }
        $view->set($configs);
   }
}
