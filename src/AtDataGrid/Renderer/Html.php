<?php

namespace AtDataGrid\Renderer;

use Zend\View\Model\ViewModel;

class Html extends AbstractRenderer
{
    /**
     * Html template
     *
     * @var string
     */
    protected $template = 'at-datagrid/grid';

    /**
     * Additional css file path
     *
     * @var string
     */
    protected $customCss;

    /**
     * Additional js file path
     *
     * @var string
     */
    protected $customJs;

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
    public function setCustomCss($customCss)
    {
        $this->customCss = $customCss;
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
    public function setCustomJs($customJs)
    {
        $this->customJs = $customJs;
    }

    /**
     * @param array $variables
     * @return mixed|ViewModel
     */
    public function render($variables = [])
    {
        $variables['customCss'] = $this->getCustomCss();
        $variables['customJs'] = $this->getCustomJs();

        $viewModel = new ViewModel($variables);
        $viewModel->setTemplate($this->getTemplate());

        return $viewModel;
    }
}