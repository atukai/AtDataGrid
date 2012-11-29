<?php

namespace AtDataGrid\DataGrid\Column\Decorator;

class Image extends AbstractDecorator
{
    /**
     * Image path template (local or url)
     *
     * @var string
     */
    protected $pathTemplate;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;
    
    /**
     * @var array
     */
    protected $params = array();
    
    /**
     * @param  $content
     * @param  $row
     * @return string
     */
    public function render($content, $row)
    {
        $params = array();
        
        foreach ($this->params as $key => $param) {
                $params[$key] = $param instanceof ATF_DataGrid_Column
                              ? $row[$param->getName()]
                              : $param;
        }

        if ($this->pathTemplate) {
            $path = vsprintf($this->pathTemplate, $params);
        } else {
            $path = $content;
        }

        $width  = isset($this->width) ? " width=" . $this->width . " " : "";
        $height = isset($this->height) ? " height=" . $this->height . " " : "";

        $placement = $this->getPlacement();
        $separator = $this->getSeparator();
        $image = '<img src="' . $path . '"' . $width . $height . '"/>';

        switch ($placement) {
            case self::PREPEND:
                return $image .  $separator . $content;
            case self::APPEND:
                return $content . $separator . $image;
            case self::REPLACE:
            default:
                return $image;
        }
    }

    /**
     * Set path to display image
     */
    public function setPathTemplate($template)
    {
        $this->pathTemplate = $template;
        return $this;
    }
    
    /**
     * Set params for url
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param $width
     * @return ATF_DataGrid_Column_Decorator_Image
     */
    public function setWidth($width)
    {
        $this->width = (int) $width;
        return $this;
    }

    /**
     * @param $height
     * @return ATF_DataGrid_Column_Decorator_Image
     */
    public function setHeight($height)
    {
        $this->height = (int) $height;
        return $this;
    }
}