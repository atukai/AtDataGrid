<?php

namespace AtDataGrid\DataSource\ZendDb;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Metadata\Metadata;
use Zend\Db\Sql\Select;
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
     * @var Adapter
     */
    protected $dbAdapter;

    /**
     * @var Select
     */
    protected $select;

    /**
     * Base table columns
     *
     * @var array
     */
    protected $tableColumns = array();

    /**
     * Joined tables
     *
     * @var array
     */
    protected $joinedTables = array();

    /**
     * Joined table columns
     *
     * @var array
     */
    protected $joinedColumns = array();

    /**
     * @param ZendTableGateway $table
     */
    public function __construct(ZendTableGateway $tableGateway)
	{
        $this->tableGateway = $tableGateway;
        $this->dbAdapter = $tableGateway->getAdapter();
        $this->select = $this->getTableGateway()->getSql()->select();
        $this->paginatorAdapter = new DbSelectPaginatorAdapter($this->select, $this->dbAdapter);
	}

    /**
     * @return ZendTableGateway
     */
    public function getTableGateway()
    {
        return $this->tableGateway;
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
     * @param $tableClassName
     * @param $alias
     * @param $keyName
     * @param $foreignKeyName
     * @param null $columns
     * @throws \Exception
     */
    public function with($joinedTableName, $alias, $keyName, $foreignKeyName, $columns = null)
    {
        $tableMetadata = new Metadata($this->getDbAdapter());
        $joinedTableColumns = $tableMetadata->getColumns($joinedTableName);

        $joinedColumns = array();

        foreach ($joinedTableColumns as $columnObject) {
            $columnName = $columnObject->getName();

            if (null != $columns) {
                if (in_array($columnName, $columns)) {
                    $joinedColumns[$alias . '__' . $columnName] = $columnName;
                    $this->joinedColumns[] = $alias . '__' . $columnName;
                }
            } else {
                $joinedColumns[$alias . '__' . $columnName] = $columnName;
                $this->joinedColumns[] = $alias . '__' . $columnName;
            }
        }

        $this->getSelect()->join(
            array($alias => $joinedTableName),
            $this->getTableGateway()->getTable(). '.' . $keyName . ' = '. $alias . '.' . $foreignKeyName,
            $joinedColumns
        );
    }

    /**
     * @return array|mixed
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

        //$this->setCommentAsLabel($columns);

        return $columns;
    }

    /**
     * @param $columns
     * @return void
     */
    protected function setCommentAsLabel($columns)
    {
        // Get current database name
        $query = 'SELECT DATABASE();';
        $schema = $this->getDbAdapter()->query($query);

        // Set table field comments as column label.
        $select = new Select('information_schema.COLUMNS');
        $select->columns(array('name' => 'COLUMN_NAME', 'comment' => 'COLUMN_COMMENT'))
            ->where(array('TABLE_SCHEMA' => $schema))
            ->where(array('TABLE_NAME', $this->getTableGateway()->getTable()));

        $columnsInfo = $this->getDbAdapter()->query($select->getSqlString(), Adapter::QUERY_MODE_EXECUTE);

        if ($columnsInfo) {
            foreach ($columnsInfo as $column) {
                if (!empty($column['comment'])) {
                    $columns[$column['name']]->setLabel($column['comment']);
                }
            }
        }
    }

    /**
     * @param $order
     * @return $this|mixed
     */
    public function prepare($order)
    {
        if ($order) {
            $orderParts = explode(' ', $order);
            if (in_array($orderParts[0], $this->tableColumns)) {
                $order = $this->getTableGateway()->getTable() . '.' . $order;
            }
            $this->getSelect()->order($order);
        }

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
        return $this->getTableGateway()->select(array($this->getIdentifierFieldName() => $key))->current()->getArrayCopy();
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