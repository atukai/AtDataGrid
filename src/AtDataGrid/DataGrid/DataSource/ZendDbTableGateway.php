<?php

namespace AtDataGrid\DataGrid\DataSource;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\ResultSet\ResultSet;
use AtDataGrid\DataGrid\Column;

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
        $this->select = $this->tableGateway->getSql()->select();
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
        return $this->select;
    }

    /**
     * @return mixed|\Zend\Paginator\Paginator
     */
    public function getPaginator()
    {
        if (!$this->paginator) {
            $this->paginator = new \Zend\Paginator\Paginator(
                new \Zend\Paginator\Adapter\DbSelect($this->getSelect(), $this->getDbAdapter())
            );
        }
        return $this->paginator;
    }

    /**
     * Join other table and collect joined columns
     *
     * @param $tableClass
     * @param $alias
     * @param $on
     * @param null $columns
     * @return void
     */
    public function with($tableClass, $alias, $keyName, $foreignKeyName, $columns = null)
    {
        $joinTable = new $tableClass;
        $joinTableName = $joinTable->info(Zend_Db_Table_Abstract::NAME);
        $joinTableCols = $joinTable->info(Zend_Db_Table_Abstract::COLS);

        $this->joinedTables[$alias] = $tableClass;

        // Колонки из приджойненных таблиц
        $joinedColumns = array();        
        
        foreach ($joinTableCols as $col) {
        	// Добавляем только указанные колонки
            if (null != $columns) {
                if (in_array($col, $columns)) {
                   $joinedColumns[] = $alias . '.' . $col . ' AS ' . $alias . '__' . $col;
                   $this->joinedColumns[] = $alias . '__' . $col;
                }
            // Добавляем все колонки    
            } else {
                $joinedColumns[] = $alias . '.' . $col . ' AS ' . $alias . '__' . $col;
                $this->joinedColumns[] = $alias . '__' . $col;
            }
        }

        $this->getSelect()->join(
            array($alias => $joinTableName),
            $this->getTable()->getName(). '.' . $keyName . '='. $alias . '.' . $foreignKeyName,
            $joinedColumns);
    }

    /**
     * @return array
     */
    public function loadColumns()
    {
        $columns = array();
        $tableMetadata = new \Zend\Db\Metadata\Metadata($this->getDbAdapter());
        $baseTableColumns = $tableMetadata->getColumns($this->getTableGateway()->getTable());
        //$baseTableColumns = $this->getTableGateway()->getColumns();

        // Setup default settings for base table column fields
        foreach ($baseTableColumns as $columnObject) {
            $columnName = $columnObject->getName();
        	$columnDataType = $columnObject->getDataType();

            $this->tableColumns[] = $columnName;

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
        $schema = $this->getTableGateway()->getAdapter()->fetchOne($query);

        // Set table field comments as column label.
        $select = $this->getTableGateway()->getAdapter()->select();
        $select->from('information_schema.COLUMNS', array('name' => 'COLUMN_NAME', 'comment' => 'COLUMN_COMMENT'));
        $select->where('TABLE_SCHEMA = ?', $schema);
        $select->where('TABLE_NAME = ?', $this->getTableGateway()->getTable());
        
        $columnsInfo = $select->query()->fetchAll();
        $select->reset(); // ???
        
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
     * @return mixed
     */
    public function fetch($listType, $order, $currentPage, $itemsPerPage, $pageRange)
    {
    	if ($listType == AbstractDataSource::LIST_TYPE_PLAIN) {
            if ($order) {
                $this->getSelect()->order($order);
            }

	        $paginator = $this->getPaginator();
	        $paginator->setCurrentPageNumber($currentPage)
                      ->setItemCountPerPage($itemsPerPage)
                      ->setPageRange($pageRange);

	        return $paginator->getItemsByPage($currentPage);
    	} elseif ($listType == AbstractDataSource::LIST_TYPE_TREE) {
            // @todo: implement forming data for tree
    	    /*$items = $this->getTableGateway()->fetchAll();
            return $items;*/
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
     * @return mixed
     */
    public function update($data, $key)
    {
        return $this->getTableGateway()->update($this->cleanDataForSql($data), array($this->getIdentifierFieldName() => $key));
    }

    /**
     * @param $identifier
     * @return mixed|void
     */
    public function delete($key)
    {
        return $this->getTableGateway()->delete(array($this->getIdentifierFieldName() => $key));
    }    
}