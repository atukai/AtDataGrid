<?php

namespace AtDataGrid\Column\Decorator;

use Countable;
use Zend\Stdlib\PriorityQueue;

class DecoratorChain extends AbstractDecorator implements Countable
{
    /**
     * Default priority at which filters are added
     */
    const DEFAULT_PRIORITY = 1000;

    /**
     * Filter chain
     *
     * @var PriorityQueue
     */
    protected $decorators;

    /**
     * DecoratorChain constructor.
     */
    public function __construct()
    {
        $this->decorators = new PriorityQueue();
    }

    /**
     * @param callable $callback
     * @param int $priority
     * @return $this
     */
    public function attach(callable $callback, $priority = self::DEFAULT_PRIORITY)
    {
        if (!is_callable($callback)) {
            if (!$callback instanceof DecoratorInterface) {
                throw new \BadFunctionCallException(sprintf(
                    'Expected a valid PHP callback; received "%s"',
                    (is_object($callback) ? get_class($callback) : gettype($callback))
                ));
            }
            $callback = [$callback, 'decorate'];
        }
        $this->decorators->insert($callback, $priority);
        return $this;
    }

    /**
     * @param $value
     * @param array $params
     * @return mixed
     */
    public function decorate($value, array $params = [])
    {
        $chain = clone $this->decorators;

        $valueDecorated = $value;
        foreach ($chain as $decorator) {
            $valueDecorated = call_user_func($decorator, $valueDecorated, $params);
        }

        return $valueDecorated;
    }

    /**
     * @return PriorityQueue
     */
    public function getDecorators()
    {
        return $this->decorators;
    }

    /**
     * Return the count of attached filters
     *
     * @return int
     */
    public function count()
    {
        return count($this->filters);
    }
}
