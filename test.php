<?php
PMVC\Load::plug();
PMVC\addPlugInFolder('../');
class View_config_helperTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'view_config_helper';
    function testPlugin()
    {
        ob_start();
        print_r(PMVC\plug($this->_plug));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains($this->_plug,$output);
    }

}
