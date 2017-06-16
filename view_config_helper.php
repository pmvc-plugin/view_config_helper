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
    private $_configs;

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
        if (!empty($this->_configs)) {
            return $this->_configs;
        }
        $this->_configs =& $this->getAllViewConfigs();
        if (!$this['getConfigOnly']) {
            $view = \PMVC\plug('view');
            $view->set($this->_configs);
            if (empty($view->get('htmlTitle'))) {
                $view->set(
                    'htmlTitle',
                    \PMVC\value($this->_configs, ['I18N', 'htmlTitle'])
                );
            }
        }
        return $this->_configs;
   }

   public function onB4ProcessView($subject)
   {
        $subject->detach($this);
        $this->toView();
   }
}
