<?php

/**
 * The home manager controller for OptiPic.
 *
 */
class OptiPicHomeManagerController extends modExtraManagerController
{
    /** @var OptiPic $OptiPic */
    public $OptiPic;


    /**
     *
     */
    public function initialize()
    {
        $this->OptiPic = $this->modx->getService('OptiPic', 'OptiPic', MODX_CORE_PATH . 'components/optipic/model/');
        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['optipic:default'];
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('optipic');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->OptiPic->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->OptiPic->config['jsUrl'] . 'mgr/optipic.js');
        $this->addJavascript($this->OptiPic->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->OptiPic->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->OptiPic->config['jsUrl'] . 'mgr/widgets/items.grid.js');
        $this->addJavascript($this->OptiPic->config['jsUrl'] . 'mgr/widgets/items.windows.js');
        $this->addJavascript($this->OptiPic->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->OptiPic->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addHtml('<script type="text/javascript">
        OptiPic.config = ' . json_encode($this->OptiPic->config) . ';
        OptiPic.config.connector_url = "' . $this->OptiPic->config['connectorUrl'] . '";
        Ext.onReady(function() {MODx.load({ xtype: "optipic-page-home"});});
        </script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        $this->content .= '<div id="optipic-panel-home-div"></div>';

        return '';
    }
}