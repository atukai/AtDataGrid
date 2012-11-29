<?php

namespace AtDataGrid\DataGrid\Renderer;

/**
 * @todo Rename to AtAdmin\DataGrid\Renderer\ZendViewPhpRenderer
 */
class Html extends AbstractRenderer
{
    /**
     * View object
     *
     * @var \Zend\View\Renderer\PhpRenderer
     */
    protected $engine = null;

    /**
     * Html template
     *
     * @var string
     */
    protected $template = 'at-datagrid/grid/list';

    /**
     * Additional CSS rules
     *
     * @var string
     */
    protected $cssFile = '';

    /**
     * Set view object
     */
    public function setEngine(\Zend\View\Renderer\PhpRenderer $engine)
    {
    	$this->engine = $engine;
    	return $this;
    }

    /**
     * @return null|\Zend\View\Renderer\PhpRenderer
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @param $template
     * @return Html
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param $path
     * @return Html
     */
    public function setCssFile($path)
    {
        $this->cssFile = $path;
        return $this;
    }

    /**
     * @param array $options
     * @return
     */
    public function render($variables = array())
    {
        $engine = $this->getEngine();

        $viewModel = new \Zend\View\Model\ViewModel($variables);
        $viewModel->setTemplate($this->getTemplate());

        if (!empty($this->cssFile)) {
            //$this->getView()->headLink()->appendStylesheet($this->cssFile);
        }

        return $engine->render($viewModel);
    }
}