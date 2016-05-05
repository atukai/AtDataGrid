<?php

namespace AtDataGrid\Renderer;

use Zend\View\Model\ViewModel;

class Html implements RendererInterface
{
    /**
     * Html template
     *
     * @var string
     */
    protected $template = 'at-datagrid/grid';

    /**
     * Additional css files
     *
     * @var string
     */
    protected $customCss = [];

    /**
     * Additional js files
     *
     * @var string
     */
    protected $customJs = [];

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
     * @param string $customCss
     */
    public function addCustomCss($customCss)
    {
        $this->customCss[] = $customCss;
    }

    /**
     * @return string
     */
    public function getCustomCss()
    {
        return $this->customCss;
    }

    /**
     * @return string
     */
    public function getCustomJs()
    {
        return $this->customJs;
    }

    /**
     * @param string $customJs
     */
    public function addCustomJs($customJs)
    {
        $this->customJs[] = $customJs;
    }

    /**
     * @param array $variables
     * @return ViewModel
     */
    public function render(array $variables = [])
    {
        $variables['customCss'] = $this->getCustomCss();
        $variables['customJs'] = $this->getCustomJs();

        $viewModel = new ViewModel($variables);
        $viewModel->setTemplate($this->getTemplate());

        return $viewModel;
    }
}