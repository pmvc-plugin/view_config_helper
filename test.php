<?php
PMVC\Load::plug();
PMVC\addPlugInFolders(['../']);
class View_config_helperTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'view_config_helper';
    function setup()
    {
        $view = \PMVC\plug('view_fake',array(
            _CLASS=>'FakeTemplate'
        ));
        PMVC\option('set',_VIEW_ENGINE,'fake');
    }

    function testPlugin()
    {
        ob_start();
        print_r(PMVC\plug($this->_plug));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains($this->_plug,$output);
    }

    function testCallback()
    {
        $p = \PMVC\plug($this->_plug, [
            'callback'=>function($config){
                $config['test'] = 'test'; 
                return $config;
            }
        ]);
        $p->onB4ProcessView();
        $view = \PMVC\plug('view');
        $this->assertEquals($view->get('test'), 'test');
    }

}


class FakeTemplate extends \PMVC\PlugIn\view\ViewEngine
{
    public function process() { }
}
