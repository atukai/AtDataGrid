<?php

namespace AtDataGrid\DataSource\Doctrine;

use AtDataGrid\DataSource\AbstractDataSource;
use AtDataGrid\Column;
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
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        //$paginator = new Paginator(new DoctrinePaginator(new ORMPaginator($book)));
        $this->paginatorAdapter = new DoctrinePaginator(new Paginator());
    }

    /**
     * @return ZendTableGateway
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @return Adapter|\Zend\Db\Adapter\AdapterInterface
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * @return Select
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * Join other table and collect joined columns
     *
     * @param $joinedTableName
     * @param $alias
     * @param $keyName
     * @param $foreignKeyName
     * @param null $columns
     */
    public function with($joinedTableName, $alias, $keyName, $foreignKeyName, $columns = null)
    {
        $tableMetadata = new Metadata($this->getDbAdapter());
        $joinedTableColumns = $tableMetadata->getColumns($joinedTableName);

        $joinedColumns = array();

        foreach ($joinedTableColumns as $column) {
            $columnName = $column->getName();

            if ($columns != null && ! in_array($columnName, $columns)) {
                continue;
            }

            $fullColumnName = $alias . '__' . $columnName;

            $joinedColumns[$fullColumnName] = $columnName;
            $this->joinedColumns[$fullColumnName] = $fullColumnName;
        }

        $this->getSelect()->join(
            array($alias => $joinedTableName),
            $this->getTableGateway()->getTable(). '.' . $keyName . ' = '. $alias . '.' . $foreignKeyName,
            $joinedColumns
        );
    }

    /**
     * @return array
     */
    public function loadColumns()
    {
        $columns = array();
        $tableMetadata = new Metadata($this->getDbAdapter());
        $baseTableColumns = $tableMetadata->getColumns($this->getTableGateway()->getTable());

        // Setup default settings for base table column fields
        foreach ($baseTableColumns as $column) {
            $columnName = $column->getName();
            $columnDataType = $column->getDataType();

            $this->tableColumns[] = $columnName;

            // @todo Move it to separate class
            switch (true) {
                case in_array($columnDataType, array('datetime', 'timestamp', 'time')):
                    $column = new Column\DateTime($columnName);
                    break;

                case in_array($columnDataType, array('date', 'year')):
                    $column = new Column\Date($columnName);
                    break;

                case in_array($columnDataType, array('mediumtext', 'text')):
                    $column = new Column\Textarea($columnName);
                    break;

                default:
                    $column = new Column\Literal($columnName);
                    break;
            }

            $column->setLabel($columnName);

            $columns[$columnName] = $column;
        }

        // Setup default settings for joined table column fields
        foreach ($this->joinedColumns as $columnName) {
            $column = new Column\Literal($columnName);
            $column->setLabel($columnName);

            $columns[$columnName] = $column;
        }

        $this->setCommentAsLabel($columns);

        return $columns;
    }

    /**
     * @param $columns
     */
    protected function setCommentAsLabel($columns)
    {
        $query = 'SELECT COLUMN_NAME as name, COLUMN_COMMENT as comment FROM information_schema.COLUMNS
                  WHERE TABLE_SCHEMA = "' . $this->getDbAdapter()->getCurrentSchema() . '" AND TABLE_NAME = "' . $this->getTableGateway()->getTable() . '"';

        $columnsInfo = $this->getDbAdapter()->query($query, Adapter::QUERY_MODE_EXECUTE);
        if ($columnsInfo) {
            foreach ($columnsInfo as $info) {
                if (!empty($info['comment'])) {
                    $columns[$info['name']]->setLabel($info['comment']);
                }
            }
        }
    }

    /**
     * @param $order
     * @param array $filters
     * @return $this|mixed
     */
    public function prepare($order, $filters = array())
    {
        /**
         * Filtering
         */
        foreach ($filters as $columnName => $filter) {
            $filter->apply($this->getSelect(), $columnName, $filter->getValue());
        }

        /**
         * Sorting
         */
        if ($order) {
            $orderParts = explode(' ', $order);
            if (in_array($orderParts[0], $this->tableColumns)) {
                $order = $this->getTableGateway()->getTable() . '.' . $order;
            }
            $this->getSelect()->order($order);
        }

        $this->getEventManager()->trigger(self::EVENT_DATASOURCE_PREPARE_POST, $this->getSelect());

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
        return $this->getTableGateway()->select(array($this->getIdentifierFieldName() => $key))->current();
    }

    /**
     * Get only fields which present in table
     *
     * @param array $data
     * @return array
     */
    protected function cleanDataForSql($data = array())
    {
        $cleanData = array();
        foreach ($data as $key => $value) {
            if (in_array($key, $this->tableColumns)) {
                $cleanData[$key] = $value;
            }
        }

        return $cleanData;
    }

    /**
     * @param $data
     * @return int|mixed
     */
    public function insert($data)
    {
        $table = $this->getTableGateway();
        $table->insert($this->cleanDataForSql($data));

        return $table->getLastInsertValue();
    }

    /**
     * @param $data
     * @param $key
     * @return int|mixed
     */
    public function update($data, $key)
    {
        return $this->getTableGateway()->update($this->cleanDataForSql($data), array($this->getIdentifierFieldName() => $key));
    }

    /**
     * @param $key
     * @return int|mixed
     */
    public function delete($key)
    {
        return $this->getTableGateway()->delete(array($this->getIdentifierFieldName() => $key));
    }
}