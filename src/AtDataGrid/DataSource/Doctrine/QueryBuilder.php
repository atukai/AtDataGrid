<?php

namespace AtDataGrid\DataSource\Doctrine;

use AtDataGrid\DataSource\AbstractDataSource;
use AtDataGrid\Column;
use AtDataGrid\Filter\Doctrine2Filter;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;

class QueryBuilder extends AbstractDataSource
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $qb;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @param EntityManager $em
     * @param $entityName
     */
    public function __construct(EntityManager $em, $entityName)
    {
        $this->em = $em;
        $this->qb = $this->em->createQueryBuilder();
        $this->entityName = $entityName;
        $this->qb->select('f')->from($entityName, 'f');

        $this->paginatorAdapter = new DoctrinePaginator(new Paginator($this->qb));
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->qb;
    }

    /**
     * @return array
     */
    public function loadColumns()
    {
        $columns = [];
        $classMetadata = $this->em->getClassMetadata($this->entityName);
        $baseTableColumns = $classMetadata->getFieldNames();

        // Setup default settings for base table column fields
        foreach ($baseTableColumns as $columnName) {
            $columnDataType = $classMetadata->getTypeOfField($columnName);

            $this->tableColumns[] = $columnName;

            // @todo Move it to separate class
            switch (true) {
                case in_array($columnDataType, array('datetime', 'timestamp', 'time')):
                    $column = new Column\DateTime($columnName);
                    break;

                case in_array($columnDataType, array('date', 'year')):
                    $column = new Column\Date($columnName);
                    break;

                case in_array($columnDataType, array('mediumtext', 'text', 'longtext')):
                    $column = new Column\Textarea($columnName);
                    break;

                default:
                    $column = new Column\Literal($columnName);
                    break;
            }

            $column->setLabel($columnName);

            $columns[$classMetadata->getColumnName($columnName)] = $column;
        }

        $joinedColumns = $classMetadata->getAssociationNames();
        foreach ($joinedColumns as $columnName) {
            $column = new Column\Literal($classMetadata->getSingleAssociationJoinColumnName($columnName));
            $column->setLabel($columnName);

            $columns[$classMetadata->getSingleAssociationJoinColumnName($columnName)] = $column;
        }

        return $columns;
    }

    /**
     * @param $order
     * @param array $filters
     * @return $this|mixed
     */
    public function prepare($order, $filters = [])
    {
        /**
         * Filtering
         */
        foreach ($filters as $columnName => $filter) {
            if (!$filter instanceof Doctrine2Filter) {
                throw new \RuntimeException('Doctrine/QueryBuilder data source requires Filter\Doctrine filters');
            }
            $filter->apply($this->getQueryBuilder(), $columnName, $filter->getValue());
        }

        /**
         * Sorting
         */
       /* if ($order) {
            $orderParts = explode(' ', $order);
            if (in_array($orderParts[0], $this->tableColumns)) {
                $order = $this->getTableGateway()->getTable() . '.' . $order;
            }
            $this->getSelect()->order($order);
        }*/

        $this->getEventManager()->trigger(self::EVENT_DATASOURCE_PREPARE_POST, $this->getQueryBuilder());

        //var_dump($this->getSelect()->getSqlString());exit;
        return $this;

    }

    /**
     * Return row by identifier (primary key)
     *
     * @param $key
     * @return array|mixed
     */
    public function find($key)
    {
    }

    /**
     * @param $data
     * @return int|mixed
     */
    public function insert($data)
    {
    }

    /**
     * @param $data
     * @param $key
     * @return int|mixed
     */
    public function update($data, $key)
    {
    }

    /**
     * @param $key
     * @return int|mixed
     */
    public function delete($key)
    {
    }
}