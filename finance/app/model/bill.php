<?php
// $Id$

/**
 * Bill 封装来自 bill 数据表的记录及领域逻辑
 */
class Bill extends QDB_ActiveRecord_Abstract
{

    /**
     * 返回对象的定义
     *
     * @static
     *
     * @return array
     */
    static function __define()
    {
        return array
        (
            // 指定该 ActiveRecord 要使用的行为插件
            'behaviors' => '',

            // 指定行为插件的配置
            'behaviors_settings' => array
            (
                # '插件名' => array('选项' => 设置),
            ),

            // 用什么数据表保存对象
            'table_name' => 'bill',

            // 指定数据表记录字段与对象属性之间的映射关系
            // 没有在此处指定的属性，QeePHP 会自动设置将属性映射为对象的可读写属性
            'props' => array
            (
                // 主键应该是只读，确保领域对象的“不变量”
                'id' => array('readonly' => true),

                /**
                 *  可以在此添加其他属性的设置
                 */
                # 'other_prop' => array('readonly' => true),

                /**
                 * 添加对象间的关联
                 */
                # 'other' => array('has_one' => 'Class'),

            ),

            /**
             * 允许使用 mass-assignment 方式赋值的属性
             *
             * 如果指定了 attr_accessible，则忽略 attr_protected 的设置。
             */
            'attr_accessible' => '',

            /**
             * 拒绝使用 mass-assignment 方式赋值的属性
             */
            'attr_protected' => 'id',

            /**
             * 指定在数据库中创建对象时，哪些属性的值不允许由外部提供
             *
             * 这里指定的属性会在创建记录时被过滤掉，从而让数据库自行填充值。
             */
            'create_reject' => '',

            /**
             * 指定更新数据库中的对象时，哪些属性的值不允许由外部提供
             */
            'update_reject' => '',

            /**
             * 指定在数据库中创建对象时，哪些属性的值由下面指定的内容进行覆盖
             *
             * 如果填充值为 self::AUTOFILL_TIMESTAMP 或 self::AUTOFILL_DATETIME，
             * 则会根据属性的类型来自动填充当前时间（整数或字符串）。
             *
             * 如果填充值为一个数组，则假定为 callback 方法。
             */
            'create_autofill' => array
            (
                # 属性名 => 填充值
                # 'is_locked' => 0,
            ),

            /**
             * 指定更新数据库中的对象时，哪些属性的值由下面指定的内容进行覆盖
             *
             * 填充值的指定规则同 create_autofill
             */
            'update_autofill' => array
            (
            ),

            /**
             * 在保存对象时，会按照下面指定的验证规则进行验证。验证失败会抛出异常。
             *
             * 除了在保存时自动验证，还可以通过对象的 ::meta()->validate() 方法对数组数据进行验证。
             *
             * 如果需要添加一个自定义验证，应该写成
             *
             * 'title' => array(
             *        array(array(__CLASS__, 'checkTitle'), '标题不能为空'),
             * )
             *
             * 然后在该类中添加 checkTitle() 方法。函数原型如下：
             *
             * static function checkTitle($title)
             *
             * 该方法返回 true 表示通过验证。
             */
            'validations' => array
            (
                'drug_id' => array
                (
                    array('max_length', 255, '药品ID不能超过 255 个字符'),

                ),

                'bill' => array
                (
                    array('is_int', '单据日期必须是一个整数'),

                ),

                'billnumber' => array
                (
                    array('max_length', 255, '单据编号不能超过 255 个字符'),

                ),

                'billtype' => array
                (
                    array('max_length', 255, '单据类型不能超过 255 个字符'),

                ),

                'location' => array
                (
                    array('max_length', 255, '货位不能超过 255 个字符'),

                ),

                'unit' => array
                (
                    array('max_length', 255, '单位不能超过 255 个字符'),

                ),

                'quantity' => array
                (
                    array('is_int', '数量必须是一个整数'),

                ),

                'intoquantity' => array
                (
                    array('is_int', '入库数量必须是一个整数'),

                ),

                'exitquantity' => array
                (
                    array('is_int', '出库数量必须是一个整数'),

                ),

                'cost' => array
                (
                    array('is_int', '成本必须是一个整数'),

                ),

                'costamount' => array
                (
                    array('is_int', '成本金额必须是一个整数'),

                ),

                'price' => array
                (
                    array('is_int', '单价必须是一个整数'),

                ),

                'amount' => array
                (
                    array('is_int', '金额必须是一个整数'),

                ),

                'grossprofit' => array
                (
                    array('is_int', '毛利必须是一个整数'),

                ),

                'tradeunit' => array
                (
                    array('max_length', 255, '往来单位不能超过 255 个字符'),

                ),

                'handleperson' => array
                (
                    array('max_length', 255, '经手人不能超过 255 个字符'),

                ),

                'singler' => array
                (
                    array('max_length', 255, '制单人不能超过 255 个字符'),

                ),


            ),
        );
    }


/* ------------------ 以下是自动生成的代码，不能修改 ------------------ */

    /**
     * 开启一个查询，查找符合条件的对象或对象集合
     *
     * @static
     *
     * @return QDB_Select
     */
    static function find()
    {
        $args = func_get_args();
        return QDB_ActiveRecord_Meta::instance(__CLASS__)->findByArgs($args);
    }

    /**
     * 返回当前 ActiveRecord 类的元数据对象
     *
     * @static
     *
     * @return QDB_ActiveRecord_Meta
     */
    static function meta()
    {
        return QDB_ActiveRecord_Meta::instance(__CLASS__);
    }


/* ------------------ 以上是自动生成的代码，不能修改 ------------------ */

}

