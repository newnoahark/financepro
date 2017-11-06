<?php

/**
 * Help_Session 类提供将 session 保存到数据库的能力
 *
 * 要使用 Help_Session，必须完成下列准备工作：
 *
 * - 创建需要的数据表
 *
 *	 字段名	   类型			 用途
 *	 sess_id	 varchar(64)	 存储 session id
 *	 sess_data   text			存储 session 数据
 *	 activity	int(11)		 该 session 最后一次读取/写入时间
 *	 user_id	 text			存储 登陆的用户ID
 * 
 * - 表类型用 InnoDB 处理大量并发数据
 * 
 * @version 1.0
 */
class Helper_session
{
	/**
	 * 保存 session 的数据表模型名称
	 *
	 * @var string
	 */
	protected  $session_table = null;
	
	/**
	 * 保存 session id 的字段名
	 *
	 * @var string
	 */
	protected  $fieldId = null;
	
	/**
	 * 保存 session 数据的字段名
	 *
	 * @var string
	 */
	protected  $fieldData = null;
	
	/**
	 * 保存 session 过期时间的字段名
	 *
	 * @var string
	 */
	protected  $fieldActivity = null;
	
	/**
	 * 指示 session 的有效期
	 * 
	 * 0 表示由 PHP 运行环境决定，其他数值为超过最后一次活动时间多少秒后失效
	 *
	 * @var int
	 */
	protected  $lifeTime = 0;
	
	/**
	 * 构造函数
	 *
	 */
	function __construct()
	{
		$this->session_table = 'session';
		$this->fieldId = 'sid';
		$this->fieldData = 'userdata';
		$this->fieldActivity = 'activity';
		$this->lifeTime = (int)ini_get('session.gc_maxlifetime');
		$this->table_model = QDB_ActiveRecord_Meta::instance($this->session_table);

		
		session_set_save_handler(
			array(& $this, 'sessionOpen'),
			array(& $this, 'sessionClose'),
			array(& $this, 'sessionRead'),
			array(& $this, 'sessionWrite'),
			array(& $this, 'sessionDestroy'),
			array(& $this, 'sessionGc')
		);
	}
	
	/**
	 * 析构函数
	 */
	function __destruct()
	{
		session_write_close();
	}
	
	/**
	 * 打开 session
	 *
	 * @param string $savePath
	 * @param string $sessionName
	 *
	 * @return boolean
	 */
	function sessionOpen($savePath, $sessionName)
	{
		$this->sessionGc($this->lifeTime);
	}

	/**
	 * 关闭 session
	 *
	 * @return boolean
	 */
	function sessionClose()
	{
		return true;
	}

	/**
	 * 读取指定 id 的 session 数据
	 *
	 * @param string $sessid
	 *
	 * @return string
	 */
	function sessionRead($sessid)
	{
		$time = time()-$this->lifeTime;
		
		$ret = QDB_ActiveRecord_Meta::instance($this->session_table)
			->find($this->fieldId.' = ? and '.$this->fieldActivity.' >= ?',$sessid,$time)->setColumns($this->fieldData)
			->asArray()->getOne();
		// 返回 fieldData 字段
		return $ret[$this->fieldData];
	}

	/**
	 * 写入指定 id 的 session 数据
	 *
	 * @param string $sessid
	 * @param string $data
	 *
	 * @return boolean
	 */
	function sessionWrite($sessid, $data)
	{
		// 设置基本数据
		$changeprops = array($this->fieldData => $data, $this->fieldActivity => CURRENT_TIMESTAMP);
	
		$fields = (array)$this->_beforeWrite($sessid);
		if($data == '')
		{
			$fields = array('uid' => '0');
		}
		$changeprops = array_merge($changeprops,$fields);
		
		// 查询表中对应 ID 的记录
		$count = QDB_ActiveRecord_Meta::instance($this->session_table)->find($this->fieldId.' = ?',$sessid)->getCount();
		// 如果记录数大于 0则修改记录
		if($count > 0)
		{
			try
			{
				$session = QDB_ActiveRecord_Meta::instance($this->session_table)
					->find($this->fieldId.' = ?',$sessid)->getOne();
				$session->changeProps($changeprops);
				$session->save();
			}
			catch (QDB_ActiveRecord_ValidateFailedException $ex)
			{
				dump($ex);
			}
		}
		else 
		{
			try
			{
				Session::meta()->table->insert(array_merge($changeprops, array($this->fieldId =>$sessid)), false);
			}
			catch (QDB_ActiveRecord_ValidateFailedException $ex)
			{
				dump($ex);
			}
		}
	}

	/**
	 * 销毁指定 id 的 session
	 *
	 * @param string $sessid
	 *
	 * @return boolean
	 */
	function sessionDestroy($sessid)
	{
		$session = QDB_ActiveRecord_Meta::instance($this->session_table)->find($this->fieldId.' = ?',$sessid)->getOne();
		$session->destroy();
		return true;
	}

	/**
	 * 清理过期的 session 数据
	 *
	 * @param int $maxlifetime
	 *
	 * @return boolean
	 */
	function sessionGc($maxlifetime)
	{
		QDB_ActiveRecord_Meta::instance($this->session_table)
			->destroyWhere($this->fieldActivity.' < '.(time()-$this->lifeTime));
		return true;
	}
	

	/**
	 * 返回要写入 session 的额外内容，开发者应该在继承类中覆盖此方法
	 *
	 * 例如返回：
	 * return array(
	 *	  'username' => $username
	 * );
	 *
	 * 数据表中要增加相应的 username 字段。
	 *
	 * @param string $sessid
	 *
	 * @return array
	 */
	function _beforeWrite($sessid)
	{
		$key = Q::ini('acl_session_key');
		$session = isset($_SESSION[$key]) ? $_SESSION[$key] : null;
		if($session == null)
		{
			return array();
		}else {
			return array('uid'=>$session['id']);
		}
	}


    /**
     +----------------------------------------------------------
     * 设置Session gc_maxlifetime值
     * 返回之前设置
     +----------------------------------------------------------
     * @param string $gc_maxlifetime
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    static function setGcMaxLifetime($gcMaxLifetime = null) {
        if (isset($gcMaxLifetime) && is_int($gcMaxLifetime) && $gcMaxLifetime >= 1) {
            ini_set('session.gc_maxlifetime', $gcMaxLifetime);
        }
    }

    /**
     +----------------------------------------------------------
     * 暂停Session
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    static function pause() {
        session_write_close();
    }

    /**
     +----------------------------------------------------------
     * 检查Session 值是否已经设置
     +----------------------------------------------------------
     * @param string $name
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    static function is_set($name) {
        return isset($_SESSION[$name]);
    }

    /**
     +----------------------------------------------------------
     * 取得当前项目的Session 值
     * 返回之前设置
     +----------------------------------------------------------
     * @param string $name
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    static function get($name) {
        if(isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }else {
            return null;
        }
    }

    /**
     +----------------------------------------------------------
     * 设置当前项目的Session 值
     * 返回之前设置
     +----------------------------------------------------------
     * @param string $name
     * @param mixed  $value
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    static function set($name, $value) {
        if (null === $value) {
            unset($_SESSION[$name]);
        } else {
            $_SESSION[$name] = $value;
        }
    }
	
}