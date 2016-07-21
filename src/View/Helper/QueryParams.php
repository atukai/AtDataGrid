<?php

namespace AtDataGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;

class QueryParams extends AbstractHelper
{
    /**
     * @param array $params
     * @param bool $reuseMatchedParams
     * @param bool $resetCurrentParams
     * @return string
     */
    public function __invoke($params = [], $reuseMatchedParams = true, $resetCurrentParams = false)
    {
        $currentParams = [];
        if (!$resetCurrentParams) {
            $queryString = $_SERVER['QUERY_STRING'];
            $currentParamPairs = explode('&', $queryString);
            if (!empty($currentParamPairs[0])) {
                foreach ($currentParamPairs as $pair) {
                    $data = explode('=', $pair);
                    $currentParams[$data[0]] = urldecode($data[1]);
                }
            }
        }
        $queryString = http_build_query(array_merge($currentParams, $params));
        return $this->getView()->url(null, [], [], $reuseMatchedParams) . '?' . $queryString;
    }
}