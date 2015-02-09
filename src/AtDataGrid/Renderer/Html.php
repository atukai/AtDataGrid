<?php

namespace AtDataGrid\Renderer;

use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

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
     * @param array $variables
     * @return mixed|ViewModel
     */
    public function render($variables = [])
    {
        if ($this->getCustomCss()) {
            $variables['customCss'] = $this->getCustomCss();
        }

        $viewModel = new ViewModel($variables);
        $viewModel->setTemplate($this->getTemplate());

        return $viewModel;
    }
}