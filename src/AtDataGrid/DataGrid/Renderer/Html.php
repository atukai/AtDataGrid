<?php

namespace AtDataGrid\DataGrid\Renderer;

use Zend\View\Renderer\RendererInterface;

/**
 * Class Html
 * @package AtDataGrid\DataGrid\Renderer
 */
class Html extends AbstractRenderer
{
    /**
     * Template rendering engine
     * @var \Zend\View\Renderer\RendererInterface
     */
    protected $engine;

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
     * @param \Zend\View\Renderer\RendererInterface $engine
     * @return $this
     */
    public function setEngine(RendererInterface $engine)
    {
    	$this->engine = $engine;
    	return $this;
    }

    /**
     * @return \Zend\View\Renderer\RendererInterface
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @param $template
     * @return $this
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
     * @return $this
     */
    public function setCssFile($path)
    {
        $this->cssFile = $path;
        return $this;
    }

    /**
     * @param array $variables
     * @return string
     */
    public function render($variables = array())
    {
        $engine = $this->getEngine();

        $viewModel = new \Zend\View\Model\ViewModel($variables);
        $viewModel->setTemplate($this->getTemplate());

        /*if (!empty($this->cssFile)) {
            $this->getView()->headLink()->appendStylesheet($this->cssFile);
        }*/

        return $engine->render($viewModel);
    }
}