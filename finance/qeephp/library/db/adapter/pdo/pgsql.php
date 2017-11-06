<?php
// $Id: pgsql.php 2175 2009-02-02 06:24:38Z yangyi $

/**
 * 定义 QDB_Adapter_Pdo_Pgsql 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: pgsql.php 2175 2009-02-02 06:24:38Z yangyi $
 * @package database
 */

/**
 * QDB_Adapter_Pdo_Pgsql 类提供对 PostgreSQL 的支持
 *
 * @author yangyi.cn.gz@gmail.com
 * @version $Id: pgsql.php 2175 2009-02-02 06:24:38Z yangyi $
 * @package database
 */
class QDB_Adapter_Pdo_Pgsql extends QDB_Adapter_Pdo_Abstract {
    protected $_pdo_type = 'pgsql';

    public function nextID($table_name, $field_name, $start_value = 1) {
        $table_parts = $this->_parseTableName($table_name);
        $seqName = sprintf('%s_%s_seq', $table_parts['table'], $field_name);
        if ($table_parts['schema']) { $seqName = sprintf('"%s"."%s"', $table_parts['schema'], $seqName); }

        $next_sql = sprintf("SELECT NEXTVAL('%s')", $seqName);

        try {
            $next_id = $this->execute($next_sql)->fetchOne();
        } catch (QDB_Exception $e) {
            if (!$this->createSeq($seqName, $start_value)) { return false; }

            $next_id = $this->execute($next_sql)->fetchOne();
        }

        $this->_insert_id = $next_id;
        return $this->_insert_id;
    }

    public function createSeq($seq_name, $start_value = 1) {
        return  $this->execute(sprintf('CREATE SEQUENCE %s START %s', $seqname, $start_value));
    }

    public function dropSeq($seq_name) {
        return $this->execute(sprintf('DROP SEQUENCE %s', $seqname));
    }

    public function insertID() {
        if (!$this->isConnected()) { return false; }
        try {
            $this->_insert_id = $this->execute('SELECT LASTVAL()')->fetchOne();
            return $this->_insert_id;
        } catch (QDB_Exception $e) {
            return null;
        }
    }

    public function identifier($name) {
        $name = trim($name, '"');
        return ($name != '*') ? "\"{$name}\"" : '*';
    }

    function selectLimit($sql, $offset = 0, $length = 30, array $inputarr = null)
    {
        if (strtoupper($length) != 'ALL') { $length = (int)$length; }
        $sql = sprintf('%s LIMIT %s OFFSET %d', $sql, $length, $offset);
        return $this->execute($sql, $inputarr);
    }

    public function qtable($table_name, $schema = null, $alias = null) {
        if (is_array($table_name)) {    // 如果是$this->_parseTableName()的返回结果
            if (array_key_exists('table', $table_name)) {
                $schema_name = $table_name['schema'];
                $table_name = $table_name['table'];
            } else {
                return $table_name;
            }
        } else {
            $table_parts = $this->_parseTableName($table_name, $schema);
            $table_name = $table_parts['table'];
            $schema_name = $table_parts['schema'];
        }

        //public 是默认的schema
        if (strtoupper($schema_name) == 'PUBLIC') { $schema_name = null; }
        $i = empty($schema_name)
           ? "\"{$table_name}\""
           : "\"{$schema_name}\".\"{$table_name}\"";

        return empty($alias) ? $i : $i . " \"{$alias}\"";
    }

    /**
     * 启动事务
     */
    function startTrans()
    {
        if (!$this->_transaction_enabled) { return false; }

        if ($this->_trans_count == 0) {
            $this->execute('BEGIN;');
            $this->_has_failed_query = false;
        } elseif ($this->_trans_count && $this->_savepoint_enabled) {
            $savepoint = 'savepoint_'. $this->_trans_count;
            $this->execute("SAVEPOINT {$savepoint};");
            array_push($this->_savepoints_stack, $savepoint);
        }

        ++$this->_trans_count;
        return true;
    }

    /**
     * 完成事务，根据查询是否出错决定是提交事务还是回滚事务
     *
     * 如果 $commit_on_no_errors 参数为 true，当事务中所有查询都成功完成时，则提交事务，否则回滚事务
     * 如果 $commit_on_no_errors 参数为 false，则强制回滚事务
     *
     * @param boolean $commit_on_no_errors 指示在没有错误时是否提交事务
     */
    function completeTrans($commit_on_no_errors = true)
    {
        if ($this->_trans_count == 0)
        {
            return;
        }

        -- $this->_trans_count;
        if ($this->_trans_count == 0)
        {
            if ($this->_has_failed_query == false && $commit_on_no_errors)
            {
                $this->execute('COMMIT');
            }
            else
            {
                $this->execute('ROLLBACK');
            }
        }
        elseif ($this->_savepoint_enabled)
        {
            $savepoint = array_pop($this->_savepoints_stack);
            if ($this->_has_failed_query || $commit_on_no_errors == false)
            {
                $this->execute("ROLLBACK TO SAVEPOINT {$savepoint}");
            }
            else
            {
                $this->execute("RELEASE SAVEPOINT {$savepoint}");
            }
        }
    }

    /**
     * 返回指定数据表（或者视图）的元数据
     *
     * 返回的结果是一个二维数组，每一项为一个字段的元数据。
     * 每个字段包含下列属性：
     *
     * - name:            字段名
     * - scale:           小数位数
     * - type:            字段类型
     * - ptype:           简单字段类型（与数据库无关）
     * - length:          最大长度
     * - not_null:        是否不允许保存 NULL 值
     * - pk:              是否是主键
     * - auto_incr:       是否是自动增量字段
     * - binary:          是否是二进制数据
     * - unsigned:        是否是无符号数值
     * - has_default:     是否有默认值
     * - default:         默认值
     * - desc:            字段描述
     *
     * ptype 是下列值之一：
     *
     * - c char/varchar 等类型
     * - x text 等类型
     * - b 二进制数据
     * - n 数值或者浮点数
     * - d 日期
     * - t TimeStamp
     * - l 逻辑布尔值
     * - i 整数
     * - r 自动增量
     * - p 非自增的主键字段
     *
     * @param string $table_name
     * @param string $schema
     *
     * @return array
     */
    function metaColumns($table_name, $schema = null)
    {
        $table_parts = $this->_parseTableName($table_name, $schema);
        $schema_name = $table_parts['schema'];
        $table_name = $table_parts['table'];

        static $typeMap = array(
            'money' => 'c',
            'interval' => 'c',
            'char' => 'c',
            'character' => 'c',
            'varchar' => 'c',
            'name' => 'c',
            'bpchar' => 'c',
            '_varchar' => 'c',
            'inet' => 'c',
            'macaddr' => 'c',
            'text' => 'x',
            'image' => 'b',
            'blob' => 'b',
            'bit' => 'b',
            'varbit' => 'b',
            'bytea' => 'b',
            'bool' => 'l',
            'boolean' => 'l',
            'date' => 'd',
            'timestamp without time zone' => 't',
            'time' => 't',
            'datetime' => 't',
            'timestamp' => 't',
            'timestamptz' => 't',
            'smallint' => 'i',
            'begint' => 'i',
            'integer' => 'i',
            'int8' => 'i',
            'int4' => 'i',
            'int2' => 'i',
            'oid' => 'r',
            'serial' => 'r',
            'float'  => 'n',
            'float4' =>'n',
            'double' => 'n',
            'float8' =>'n',
            'uuid' => 'c',
            '_uuid' => 'c',
            'xml' => 'x',
            'numeric' => 'n',
        );

        $keys = $this->getAll(sprintf("SELECT ic.relname AS index_name, a.attname AS column_name,i.indisunique AS unique_key, i.indisprimary AS primary_key FROM pg_class bc, pg_class ic, pg_index i, pg_attribute a WHERE bc.oid = i.indrelid AND ic.oid = i.indexrelid AND (i.indkey[0] = a.attnum OR i.indkey[1] = a.attnum OR i.indkey[2] = a.attnum OR i.indkey[3] = a.attnum OR i.indkey[4] = a.attnum OR i.indkey[5] = a.attnum OR i.indkey[6] = a.attnum OR i.indkey[7] = a.attnum) AND a.attrelid = bc.oid AND (bc.relname = '%s' or bc.relname=lower('%s'))", $table_name, $table_name));

        $rsdefa = array();
        $sql = sprintf("SELECT d.adnum as num, d.adsrc as def from pg_attrdef d, pg_class c where d.adrelid=c.oid and (c.relname='%s' or c.relname=lower('%s')) order by d.adnum", $table_name, $table_name);
        $rsdef = $this->getAll($sql);

        if (count($rsdef)>0) {
            foreach ($rsdef as $row) {
                $num = $row['num'];
                $def = $row['def'];
                if (strpos($def, '::') === false && strpos($def, "'") === 0) {
                    $def = substr($def, 1, strlen($def) - 2);
                }
                $rsdefa[$num] = $def;
            }
            unset($rsdef);
        }
        if (!empty($schema_name)) {
            $rs = $this->execute(sprintf("SELECT a.attname, t.typname, a.attlen, a.atttypmod, a.attnotnull, a.atthasdef, a.attnum FROM pg_class c, pg_attribute a, pg_type t, pg_submodule n WHERE relkind in ('r','v') AND (c.relname='%s' or c.relname = lower('%s')) and c.relsubmodule=n.oid and n.nspname='%s' and a.attname not like '....%%' AND a.attnum > 0 AND a.atttypid = t.oid AND a.attrelid = c.oid ORDER BY a.attnum", $table_name, $table_name, $schema_name));
        }else{
            $rs = $this->execute(sprintf("SELECT a.attname,t.typname,a.attlen,a.atttypmod,a.attnotnull,a.atthasdef,a.attnum FROM pg_class c, pg_attribute a,pg_type t WHERE relkind in ('r','v') AND (c.relname='%s' or c.relname = lower('%s')) and a.attname not like '....%%' AND a.attnum > 0 AND a.atttypid = t.oid AND a.attrelid = c.oid ORDER BY a.attnum ", $table_name, $table_name));
        }
        /* @var $rs QDB_Result_Abstract */
        $retarr = array();
        $cnt111 = 0;
        $rs->fetchMode = QDB::FETCH_MODE_ARRAY;
        while ($row = $rs->fetchRow()) {
            $field = array();
            $field['default'] = '';
            $field['name'] = $row['attname'];
            $field['type'] = strtolower($row['typname']);
            $field['length'] = $row['attlen'];
            $field['attnum'] = $row['attnum'];
            if ($field['length'] <= 0) {
                $field['length'] = $row['atttypmod'] - 4;
            }
            if ($field['length'] <= 0) {
                $field['length'] = -1;
            }
            $field['scale'] = null;
            if ($field['type'] == 'numeric') {
                $field['scale'] = $field['length'] & 0xFFFF;
                $field['length'] >>= 16;
            }

            $field['has_default'] = ($row['atthasdef'] == 't');
            if ($field['has_default']) {
                $default = $rsdefa[$row['attnum']];
                $pos = strpos($default, '::');
                if (false === $pos) {
                    $field['default'] = $default;
                } else {
                    $field['default'] = trim(substr($default, 0, $pos), '\'"');
                }
            }
            else
            $field['default'] = null;
            $field['not_null'] = ($row['attnotnull'] == 't');
            $field['pk'] = false;
            $field['unique'] = false;
            if (is_array($keys)) {
                foreach($keys as $key) {
                    if ($field['name'] == $key['column_name'])
                        $field['pk']=($key['primary_key'] == 't');
                    if ($field['name'] == $key['column_name'] )
                        $field['unique'] = ( $key['unique_key'] == 't');
                }
            }
            // 这里要对几种特殊的类型的默认值进行处理
            $field['ptype'] = $typeMap[strtolower($field['type'])];
            // 这里是为了配合解决无法取得InsertID的情况。
            if ($field['ptype'] == 'r' || ($field['ptype'] == 'i' && strpos($field['default'],'nextval') !== false)) {
                $field['has_default'] = false;
                $field['default'] = null;
            }

            $field['auto_incr'] = false;
            $field['binary'] = ($field['ptype']=='b');
            $field['unsigned'] = false ;
            if (!$field['binary'] ) {
                $d = $field['default'];
                if ($d != '' && $d != 'NULL') {
                    $field['has_default'] = true;
                    $field['default'] = $d;
                } else {
                    $field['has_default'] = false;
                }
            }
            $field['desc'] = '';
            $retarr[strtolower($field['name'])] = $field;
        }
        return $retarr;
    }

    /**
     * 获得数据库中所有的表，不包括视图
     * 
     * @param string $pattern 
     * @param string $schema 
     * @access public
     * @return array
     */
    function metaTables($pattern = null, $schema = null)
    {
        $where = array('tablename NOT SIMILAR TO \'(pg_|sql_|information_)%\'');
        if ($schema) {
            $where[] = sprintf('schemaname = %s', $this->qstr($schema));
        }

        if ($pattern) {
            $where[] = sprintf('tablename ILIKE %s', $this->qstr($pattern));
        }

        $sql = sprintf('SELECT schemaname, tablename FROM pg_tables WHERE %s', implode(' AND ', $where));
        $tables = array();
        foreach ($this->getAll($sql) as $row) {
            $tables[] = $this->qtable($row['tablename'], $row['schemaname']);
        }

        return $tables;
    }

    /**
     * 获得数据库中所有的视图名称
     * 
     * @param string $pattern 
     * @param string $schema 
     * @access public
     * @return array
     */
    function metaViews($pattern = null, $schema = null) {
        $where = array('viewname NOT SIMILAR TO \'(pg_|sql_|information_)%\' AND schemaname NOT SIMILAR TO \'(pg_|sql_|information_)%\'');
        if ($schema) {
            $where[] = sprintf('schemaname = %s', $this->qstr($schema));
        }

        if ($pattern) {
            $where[] = sprintf('viewname ILIKE %s', $this->qstr($pattern));
        }

        $sql = sprintf('SELECT viewname FROM pg_views WHERE %s', implode(' AND ', $where));

        return $this->getCol($sql);
    }

    /**
     * 获得表的约束关系 
     * contype指定只查询哪些类型的约束
     * p: 主键约束
     * f: 外键约束
     * u: 唯一约束
     * 
     * @param string $table_name 
     * @param string $schema_name 
     * @param array $contype
     * @access public
     * @return array
     */
    function metaConstraints($table_name, $schema_name = null, array $contype = array()) {
        $table_parts = $this->_parseTableName($table_name, $schema_name);
        $table_name = $table_parts['table'];
        $schema_name = $table_parts['schema'];

        $constraints = array();

        $current_table_oid = $this->_tableOid($table_name, $schema_name);
        $current_table_attribute = $this->_tableAttribute($current_table_oid, 'attnum');

        // 从pg_constraint查询出表的所有约束定义
        $where = array(sprintf('conrelid = %d', $current_table_oid));
        if ($contype) {
            $where[] = sprintf('contype IN (\'%s\')', implode('\',\'', $contype));
        }
        $sql = sprintf('SELECT * FROM pg_constraint WHERE %s', implode(' AND ', $where));
        foreach ($this->getAll($sql) as $row) {
            // 约束所在的字段
            $concolumns = array();
            foreach (self::pgArrayToPhp($row['conkey']) as $attnum) {
                $concolumns[] = $current_table_attribute[$attnum]['attname'];
            }

            // 开始处理不同类型的约束
            if ('p' == $row['contype']) {  // 主键约束
                $constraints['p'] = $concolumns;
            } else if ('f' == $row['contype']) {  // 外键约束
                $fk = array(
                    'columns' => $concolumns
                );
                $referer_table_oid = $row['confrelid'];
                // 被引用的表的字段信息
                $referer_table_attribute = $this->_tableAttribute($referer_table_oid, 'attnum');
                foreach (self::pgArrayToPhp($row['confkey']) as $referer_attnum) {
                    $fk['referer_columns'][] = $referer_table_attribute[$referer_attnum]['attname'];
                }
                $referer_table_relname = $this->_tableRelname($referer_table_oid);
                $fk['referer_table'] = $this->qtable($referer_table_relname['table_name'], $referer_table_relname['schema_name']);

                $constraints['f'][] = $fk;
            } else if ('u' == $row['contype']) {  // 唯一约束
                $constraints['u'][] = $concolumns;
            }
        }
        return $constraints;
    }

    /**
     * 获得表的索引信息
     *
     * @param string $table_name
     * @param string $schema_name
     * @access public
     * @return array
     */
    public function metaIndexes($table_name, $schema_name = null) {
        $table_parts = $this->_parseTableName($table_name, $schema_name);
        $table_name = $table_parts['table'];
        $schema_name = $table_parts['schema'];
        $indexes = array();

        $current_table_oid = $this->_tableOid($table_name, $schema_name);
        $current_table_attribute = $this->_tableAttribute($current_table_oid, 'attnum');

        // 从pg_index查询出表的所有索引定义
        $sql = sprintf('SELECT * FROM pg_index WHERE indrelid = %d', $current_table_oid);
        foreach ($this->getAll($sql) as $row) {
            $idx = array();

            $indcolumns = array();
            foreach (explode(' ', $row['indkey']) as $attnum) {
                $indcolumns[] = $current_table_attribute[$attnum]['attname'];
            }
            $idx['columns'] = $indcolumns;

            $idx['is_unique'] = ('t' == $row['indisunique']);
            $idx['is_primary'] = ('t' == $row['indisprimary']);
            $indexes[] = $idx;
        }
        return $indexes;
    }

    /**
     * 从系统表中查询schema的oid
     *
     * @param string $schema_name
     * @access protected
     * @return integer
     */
    protected function _schemaOid($schema_name) {
        static $oid = array();

        if (!array_key_exists($schema_name, $oid)) {
            $sql = sprintf('SELECT oid FROM pg_submodule WHERE nspname = %s', $this->qstr($schema_name));
            $oid[$schema_name] = $this->getOne($sql);
        }

        return $oid[$schema_name];
    }

    /**
     * 从系统表中查询表的oid 
     * 
     * @param string $table_name 
     * @param string $schema_name 
     * @access protected
     * @return integer
     */
    protected function _tableOid($table_name, $schema_name = null) {
        static $oid = array();
        $table_parts = $this->_parseTableName($table_name, $schema_name);
        $table_name = $table_parts['table'];
        $schema_name = $table_parts['schema'];
        $full_table_name = $this->qtable($table_parts);

        if (!array_key_exists($full_table_name, $oid)) {
            $where = array();
            $where[] = sprintf('relname = %s', $this->qstr($table_name));
            if ($schema_name) {
                $where[] = sprintf('relsubmodule = %d', $this->_schemaOid($schema_name));
            }
            $sql = sprintf('SELECT oid FROM pg_class WHERE %s', implode(' AND ', $where));
            $oid[$full_table_name] = $this->getOne($sql);
        }

        return $oid[$full_table_name];
    }

    /**
     * 从系统表中查询指定表的字段信息
     * 
     * @param integer $table_oid 
     * @param string $result_key 
     * @access protected
     * @return array
     */
    protected function _tableAttribute($table_oid, $result_key = null) {
        $sql = sprintf('SELECT * FROM pg_attribute WHERE attrelid = %d AND attnum > 0', $table_oid);
        return $this->execute($sql)->fetchAssoc($result_key);
    }

    /**
     * 从表的oid获得表名和schema名 
     * 
     * @param integer $table_oid 
     * @access protected
     * @return array
     */
    protected function _tableRelname($table_oid) {
        static $relname = array();

        if (!array_key_exists($table_oid, $relname)) {
            $sql = sprintf('SELECT relname, relsubmodule FROM pg_class WHERE oid = %d', $table_oid);
            $row = $this->getRow($sql);
            $relname[$table_oid]['table_name'] = $row['relname'];
            $relname[$table_oid]['schema_name'] = $this->_schemaNspname($row['relsubmodule']);
        }

        return $relname[$table_oid];
    }

    /**
     * 从schema oid获得schema名字 
     * 
     * @param integer $schema_oid 
     * @access protected
     * @return string
     */
    protected function _schemaNspname($schema_oid) {
        static $nspname = array();

        if (!array_key_exists($schema_oid, $nspname)) {
            $sql = sprintf('SELECT nspname FROM pg_submodule WHERE oid = %d', $schema_oid);
            $nspname[$schema_oid] = $this->getOne($sql);
        }

        return $nspname[$schema_oid];
    }

    /**
     * 把表名称的数据库名称、schame名称和表名称解析为数组返回 
     * 解析出来的名称都已经用trim()处理过
     *
     * schema参数是为了配合以前写的nextId()、qtable()、metaColumns()函数
     * 这几个函数允许指定schema参数
     * 如果在$table_name中没有解析出schema名称，则使用schema参数的值
     * 否则使用解析出的schema名称
     * 简单来说就是$table_name的信息优先
     * 
     * @param string $table_name 
     * @access protected
     * @return array
     */
    protected function _parseTableName($table_name, $schema = null) {
        $parts = array('schema' => $schema);

        if (strpos($table_name, '.') !== false) {
            $result = explode('.', $table_name);
            if (3 == count($result)) {
                $parts['db'] = trim($result[0], '"');
                $parts['schema'] = trim($result[1], '"');
                $parts['table'] = trim($result[2], '"');
            } else {
                $parts['schema'] = trim($result[0], '"');
                $parts['table'] = trim($result[1], '"');
            }
        } else {
            $parts['table'] = trim($table_name, '"');
        }

        return $parts;
    }

    /**
     * 把postgresql数组转换为php数组
     * 仅支持一维数组
     * 
     * @param string $pgArray 
     * @access public
     * @return array
     */
    public static function pgArrayToPhp($pgArray) {
        return explode(',', trim($pgArray, '{}'));
    }

    /**
     * 把php数组转换为postgresql数组字符串 
     * 仅支持一维数组
     * 
     * @param array $phpArray 
     * @access public
     * @return string
     */
    public static function phpArrayToPg(array $phpArray) {
        return sprintf('{"%s"}', implode('","', $phpArray));
    }
}
