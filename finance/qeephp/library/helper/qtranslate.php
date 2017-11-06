<?php
class QTranslate {
	/**
	 * 使用的语言
	 *
	 * @var string
	 */
	public $use_lang;
	
	/**
	 * 程序根目录
	 *
	 * @var string
	 */
	public $root_dir;
	
	/**
	 * 应用的配置
	 *
	 * @var array
	 */
	private $app_config = array ();
	/**
	 * 配置文件
	 *
	 * @var array
	 */
	private $_config = array ();
	/**
	 * 保存当前载入的字典
	 *
	 * @var array
	 */
	private $_dict = array ();
	/**
	 * 指示哪些语言文件已经被载入
	 *
	 * @var array
	 */
	private $_loadedFiles = array ();
	
	function __construct($app_config) {
		$this->app_config = $app_config;
		$this->_config = Q::ini ( 'appini/language' );
		$autoload = $this->_config ['autoload'];
		if (! is_array ( $autoload )) {
			$autoload = explode ( ',', $autoload );
		}
		foreach ( $autoload as $load ) {
			$load = trim ( $load );
			if ($load != '') {
				$this->loadCachedDict ( $load );
			}
		}
	}
	/**
	 * 读取缓存中的语言包
	 *
	 * @param string $dName
	 * @param string $lang
	 */
	public function loadCachedDict($dictname, $language = NULL) {
		if ($language == NULL) {
			if ($this->use_lang == NULL) {
				$language = $this->_config ['default'];
			} else {
				$language = $this->use_lang;
			}
		}
		$CACHE_ID = "{$dictname}_{$language}_CACHE";
		$policy = array (
						'life_time' => $this->_config ['cache_time'], // 缓存数据失效时间是 1小时
						'serialize' => true );// 自动序列化和反序列化缓存数据

		if (! $dict = Q::cache ( $CACHE_ID, $policy )) {
			$dict = $this->loadDict ( $dictname, $language );
			if (count ( $dict )) {
				Q::writeCache ( $CACHE_ID, $dict, $policy );
			}
		}
		if (is_array ( $dict )) {
			if (! isset ( $this->_dict [$language] )) {
				$this->_dict [$language] = array ();
			}
			$this->_dict[$language] = array_merge_recursive ( $this->_dict [$language], $dict );
		}
	}
	
	/**
	 * 读取配置文件中的语言包
	 *
	 * @param unknown_type $dictname
	 * @param unknown_type $language
	 */
	public function loadDict($dictname, $language = NULL) {
		$run_mode = ! empty ( $this->app_config ['RUN_MODE'] ) ? $this->app_config ['RUN_MODE'] : Q::RUN_MODE_DEPLOY;
		$extname = ! empty ( $this->app_config ['CONFIG_FILE_EXTNAME'] ) ? $this->app_config ['CONFIG_FILE_EXTNAME'] : 'yaml';
		$root_dir = $this->app_config ['ROOT_DIR'];
		if ($language == NULL) {
			if ($this->use_lang == NULL) {
				$language = $this->_config ['default'];
			} else {
				$language = $this->use_lang;
			}
		}
		$filename = "{$root_dir}{$this->_config['dir']}{$language}/{$dictname}.{$extname}";
		if (! file_exists ( $filename )) {
			return false;
		}
		return Helper_YAML::load ( $filename );
	}
	
	public function get($key, $language = '') {
		if ($language == '') {
			if ($this->use_lang == NULL) {
				$language = $this->_config ['default'];
			} else {
				$language = $this->use_lang;
			}
		}
		return isset ( $this->_dict [$language] [$key] ) ? $this->_dict [$language] [$key] : $key;
	}
}
