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
    protected $template = 'at-datagrid/list';

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
     * @param array $variables
     * @return mixed|ViewModel
     */
    public function render($variables = array())
    {
        $viewModel = new ViewModel($variables);
        $viewModel->setTemplate($this->getTemplate());

        return $viewModel;
    }
}