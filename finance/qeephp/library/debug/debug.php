<?php
// $Id: debug.php 2008 2009-01-08 18:49:30Z dualface $

/**
 * 定义 QDebug 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: debug.php 2008 2009-01-08 18:49:30Z dualface $
 * @package debug
 */

/**
 * QDebug 为开发者提供了调试应用程序的一些辅助方法
 *
 * QDebug 对 FirePHP 这个 Firefox 插件提供了支持。
 * FirePHP 可以在运行时很方便的输出调试信息到浏览器的信息窗口。
 *
 * 要启用 QDebug 对 FirePHP 的支持，调用 QDebug::enableFirePHP() 即可。
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: debug.php 2008 2009-01-08 18:49:30Z dualface $
 * @package debug
 */
abstract class QDebug
{
    /**
     * 是否使用 FirePHP
     *
     * @var boolean
     */
    protected static $_firephp_enabled = false;

    /**
     * 启用 QDebug 对 FirePHP 的支持
     */
    static function enableFirePHP()
    {
        self::$_firephp_enabled = true;
    }

    /**
     * 禁用 QDebug 对 FirePHP 的支持
     */
    static function disableFirePHP()
    {
        self::$_firephp_enabled = false;
    }

    /**
     * 输出变量的内容
     *
     * 如果启用了 FirePHP 支持，将输出到浏览器的 FirePHP 窗口中，不影响页面输出。
     *
     * 可以使用 dump() 这个简写形式。
     *
     * @code php
     * dump($vars, '$vars current values');
     * @endcode
     *
     * @param mixed $vars 要输出的变量
     * @param string $label 标签
     * @param boolean $return 是否返回输出内容
     */
    static function dump($vars, $label = null, $return = false)
    {
        if (! $return && self::$_firephp_enabled)
        {
            QDebug_FirePHP::dump($vars, $label);
            return null;
        }

        if (ini_get('html_errors'))
        {
            $content = "<pre>\n";
            if ($label !== null && $label !== '')
            {
                $content .= "<strong>{$label} :</strong>\n";
            }
            $content .= htmlspecialchars(print_r($vars, true));
            $content .= "\n</pre>\n";
        }
        else
        {
            $content = "\n";
            if ($label !== null && $label !== '')
            {
                $content .= $label . " :\n";
            }
            $content .= print_r($vars, true) . "\n";
        }
        if ($return)
        {
            return $content;
        }

        echo $content;
        return null;
    }

    /**
     * 显示应用程序执行路径
     *
     * 如果启用了 FirePHP 支持，将输出到浏览器的 FirePHP 窗口中，不影响页面输出。
     */
    static function dumpTrace()
    {
        if (self::$_firephp_enabled)
        {
            QDebug_FirePHP::dumpTrace();
            return;
        }

        $debug = debug_backtrace();
        $lines = '';
        $index = 0;
        for ($i = 0; $i < count($debug); $i ++)
        {
            if ($i == 0)
            {
                continue;
            }
            $file = $debug[$i];
            if (! isset($file['file']))
            {
                $file['file'] = 'eval';
            }
            if (! isset($file['line']))
            {
                $file['line'] = null;
            }
            $line = "#{$index} {$file['file']}({$file['line']}): ";
            if (isset($file['class']))
            {
                $line .= "{$file['class']}{$file['type']}";
            }
            $line .= "{$file['function']}(";
            if (isset($file['args']) && count($file['args']))
            {
                foreach ($file['args'] as $arg)
                {
                    $line .= gettype($arg) . ', ';
                }
                $line = substr($line, 0, - 2);
            }
            $line .= ')';
            $lines .= $line . "\n";
            $index ++;
        } // for

        $lines .= "#{$index} {main}\n";

        if (ini_get('html_errors'))
        {
            echo nl2br(str_replace(' ', '&nbsp;', $lines));
        }
        else
        {
            echo $lines;
        }
    }
}

