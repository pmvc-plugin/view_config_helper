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
    private $_isSet = false;

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

   public function &getAllViewConfigs()
   {
        $dot = \PMVC\plug('dotenv');
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
        $dotView = \PMVC\get($this, '.env', '.env.view');
        if ($dot->fileExists($dotView)) {
            $configs = array_replace_recursive(
                $configs,
                $dot->getUnderscoreToArray($dotView)
            );
        }
        
        /*Clean disable section*/
        $sections =& \PMVC\get($configs, 'section', []);
        $features = \PMVC\getOption('features');
        foreach ($sections as $sectionKey=>$section) {
            if (empty($section['shouldRender'])) {
                unset($sections[$sectionKey]);
                continue;
            }
            $shouldRender = $section['shouldRender']; 
            if (isset($features[$shouldRender]) &&
                empty($features[$shouldRender])
            ) {
                unset($sections[$sectionKey]);
            }
        }

        if ($this['callback']) {
            $this['callback']($configs);
        }
        return $configs;
   }

   public function toView()
   {
        $view = \PMVC\plug('view');
        if ($this->_isSet) {
           $configs = $view->getRef(); 
           return $configs;
        }
        $this->_isSet = true;
        $configs =& $this->getAllViewConfigs();
        $view->set($configs);
        return $view->getRef();
   }

   public function onB4ProcessView($subject)
   {
        $subject->detach($this);
        $this->toView();
   }
}
