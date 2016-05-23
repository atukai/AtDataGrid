<?php

namespace AtDataGrid\DataSource\ZendDb;

use AtDataGrid\Filter\FilterInterface;
use AtDataGrid\Filter\ZendSqlFilter;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Metadata\Metadata;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\Db\TableGateway\TableGateway as ZendTableGateway;
use Zend\Paginator\Adapter\DbSelect as DbSelectPaginatorAdapter;
use AtDataGrid\DataSource\AbstractDataSource;
use AtDataGrid\Column;

class TableGateway extends AbstractDataSource
{
    /**
     * @var ZendTableGateway
     */
    protected $tableGateway;

    /**
     * @var Select
     */
    protected $select;

    /**
     * Base table columns
     *
     * @var array
     */
    protected $tableColumns = [];

    /**
     * Joined tables
     *
     * @var array
     */
    protected $joinedTables = [];

    /**
     * Joined table columns
     *
     * @var array
     */
    protected $joinedColumns = [];

    /**
     * @param ZendTableGateway $tableGateway
     */
    public function __construct(ZendTableGateway $tableGateway)
	{
        $tableGateway->getFeatureSet()->addFeature(new RowGatewayFeature());
        $this->tableGateway = $tableGateway;

        $this->select = $tableGateway->getSql()->select();
        $this->paginatorAdapter = new DbSelectPaginatorAdapter($this->select, $tableGateway->getAdapter());
	}

    /**
     * @return ZendTableGateway
     */
    public function getTableGateway()
    {
        return $this->tableGateway;
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

        $joinedColumns = [];

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
            [$alias => $joinedTableName],
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
                case in_array($columnDataType, ['datetime', 'timestamp', 'time']):
                    $column = new Column\DateTime($columnName);
                    break;

                case in_array($columnDataType, ['date', 'year']):
                    $column = new Column\Date($columnName);
                    break;

                case in_array($columnDataType, ['mediumtext', 'text', 'longtext']):
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
     * @param $order ['field1' => 'direction1', 'field2' => 'direction2']
     * @param array $filters
     * @return $this
     * @throws \Exception
     */
    public function prepare($order = [], $filters = [])
    {
        // Sorting
        if (!empty($order)) {
            if (in_array(key($order), $this->tableColumns)) {
                $this->getSelect()->order($order);
            }
        }

        // Filtering
        foreach ($filters as $columnName => $filter) {
            if (!$filter instanceof FilterInterface) {
                throw new \RuntimeException('Data grid filter must implements FilterInterface');
            }

            if (!$filter instanceof ZendSqlFilter) {
                throw new \RuntimeException('ZendDb/TableGateway data source requires Filter\ZendSql filters');
            }
            $filter->apply($this->getSelect(), $this->getTableGateway()->getTable(). '.' . $columnName, $filter->getValue());
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
        return $this->getTableGateway()->select([$this->getIdentifierFieldName() => $key])->current();
    }

    /**
     * Get only fields which present in table
     *
     * @param array $data
     * @return array
     */
    protected function cleanDataForSql($data = [])
    {
        $cleanData = [];
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
        return $this->getTableGateway()->update($this->cleanDataForSql($data), [$this->getIdentifierFieldName() => $key]);
    }

    /**
     * @param $key
     * @return int|mixed
     */
    public function delete($key)
    {
        return $this->getTableGateway()->delete([$this->getIdentifierFieldName() => $key]);
    }

    /**
     * @return Adapter|\Zend\Db\Adapter\AdapterInterface
     */
    protected function getDbAdapter()
    {
        return $this->tableGateway->getAdapter();
    }

    /**
     * @return AdapterInterface
     */
    public function getPaginatorAdapter()
    {
        return $this->paginatorAdapter;
    }
}