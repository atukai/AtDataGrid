<?php

namespace AtDataGrid\DataGrid\DataSource;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Metadata\Metadata;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\ResultSet\ResultSet;
use AtDataGrid\DataGrid\Column;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class ZendDbTableGateway extends AbstractDataSource
{
    /**
     * @var Adapter
     */
    protected $dbAdapter;

    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * @var \Zend\Db\Sql\Select
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
     * @param $options
     */
	public function __construct($options)
	{
		parent::__construct($options);

        $this->tableGateway = new TableGateway($options['table'], $this->getDbAdapter());
        $this->columns = $this->loadColumns();
	}

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     * @return ZendDbTableGateway
     */
    public function setDbAdapter(Adapter $adapter)
    {
        $this->dbAdapter = $adapter;
        return $this;
    }

    /**
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * @param \Zend\Db\TableGateway\TableGateway $table
     * @return ZendDbTableGateway
     */
    public function setTableGateway(TableGateway $table)
    {
        $this->tableGateway = $table;
        return $this;
    }

    /**
     * @return \Zend\Db\TableGateway\TableGateway
     */
    public function getTableGateway()
    {
        return $this->tableGateway;
    }

    /**
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect()
    {
        if ($this->select == null) {
            $this->select = $this->tableGateway->getSql()->select();
        }

        return $this->select;
    }

    /**
     * @return mixed|\Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        if (!$this->paginator) {
            $this->paginator = new Paginator(
                new DbSelect($this->getSelect(), $this->getDbAdapter())
            );
        }

        return $this->paginator;
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
     * @param $listType
     * @param $order
     * @param $currentPage
     * @param $itemsPerPage
     * @param $pageRange
     * @return mixed|\Zend\Db\ResultSet\ResultSet
     */
    public function fetch($listType, $order, $currentPage, $itemsPerPage, $pageRange)
    {
    	if ($listType == AbstractDataSource::LIST_TYPE_PLAIN) {
            if ($order) {
                $orderParts = explode(' ', $order);
                if (in_array($orderParts[0], $this->tableColumns)) {
                    $order = $this->getTableGateway()->getTable() . '.' . $order;
                }
                $this->getSelect()->order($order);
            }

            //var_dump($this->getSelect()->getSqlString());exit;

	        $paginator = $this->getPaginator();
	        $paginator->setCurrentPageNumber($currentPage)
                      ->setItemCountPerPage($itemsPerPage)
                      ->setPageRange($pageRange);
	        return $paginator->getItemsByPage($currentPage);
    	} elseif ($listType == AbstractDataSource::LIST_TYPE_TREE) {
    	    $items = $this->getTableGateway()->select();
            return $items;
    	}
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