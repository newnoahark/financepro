<?php

class Control_Ueditor extends QUI_Control_Abstract {
	/* 预设工具栏 */
	private $toolbarsOpts = array(
			'full' => '',
			'default' => "[['Undo', 'Redo', '|',
                'Bold', 'Italic', 'Underline', 'StrikeThrough', 'Superscript', 'Subscript', 'RemoveFormat', 'FormatMatch','AutoTypeSet', '|',
                'BlockQuote', '|', 'PastePlain', '|', 'ForeColor', 'BackColor', 'InsertOrderedList', 'InsertUnorderedList','SelectAll', 'ClearDoc', '|', 'CustomStyle',
                'Paragraph', '|','RowSpacingTop', 'RowSpacingBottom','LineHeight', '|','FontFamily', 'FontSize', '|',
                'DirectionalityLtr', 'DirectionalityRtl', '|', '', 'Indent', '|',
                'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyJustify', '|',
                'Link', 'Unlink', 'Anchor', '|', 'ImageNone', 'ImageLeft', 'ImageRight', 'ImageCenter', '|', 'InsertImage', 'Emotion', 'Map', 'GMap', '|',
                'Horizontal', 'Date', 'Time', 'Spechars','SnapScreen', '|',
                'InsertTable', 'DeleteTable', 'InsertParagraphBeforeTable', 'InsertRow', 'DeleteRow', 'InsertCol', 'DeleteCol', 'MergeCells', 'MergeRight', 'MergeDown', 'SplittoCells', 'SplittoRows', 'SplittoCols', '|',
                 'Print', 'Preview', 'SearchReplace']
        ]",
			'user' => "[['Undo', 'Redo', '|',
                'Bold', 'Italic', 'Underline', 'StrikeThrough', 'Superscript', 'Subscript', 'RemoveFormat', 'FormatMatch','AutoTypeSet', '|',
                'BlockQuote', '|', 'PastePlain', '|', 'ForeColor', 'BackColor', 'InsertOrderedList', 'InsertUnorderedList','SelectAll', 'ClearDoc', '|', 'CustomStyle',
                'Paragraph', '|','RowSpacingTop', 'RowSpacingBottom','LineHeight', '|','FontFamily', 'FontSize', '|',
                'DirectionalityLtr', 'DirectionalityRtl', '|', '', 'Indent', '|',
                'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyJustify', '|',
                'Link', 'Unlink', 'Anchor', '|', 'ImageNone', 'ImageLeft', 'ImageRight', 'ImageCenter', '|', 'InsertImage', 'Emotion', 'Map', 'GMap', '|',
                'Horizontal', 'Date', 'Time', 'Spechars','SnapScreen', '|',
                'InsertTable', 'DeleteTable', 'InsertParagraphBeforeTable', 'InsertRow', 'DeleteRow', 'InsertCol', 'DeleteCol', 'MergeCells', 'MergeRight', 'MergeDown', 'SplittoCells', 'SplittoRows', 'SplittoCols', '|',
                 'Print', 'Preview', 'SearchReplace']
        ]",
			'simple' => "[['Undo', 'Redo', '|', 
					'Bold', 'Italic', 'Underline', 'StrikeThrough', 'Superscript', 'Subscript', 'RemoveFormat', '|',
					'InsertOrderedList', 'InsertUnorderedList', '|',
					'Unlink', '|', 
					'Date', 'Time', 'Spechars']]"
	);
	function render() {
		
		$this->_view['id'] = $this->id;
		$this->_view['value'] = $this->value;
		$this->_view['toolbars'] = $this->toolbarsOpts[$this->toolbars];
		$this->_view['loadjs'] = $this->loadjs;
		$this->_view['height'] = $this->height;
		$this->_view['model'] = $this->model;
		$this->_view['field'] = $this->field;
		$this->_view['target'] = $this->target;
		$this->_view['post_key'] = isset($_SESSION['post_key']) ? $_SESSION['post_key'] : '';
		return $this->_fetchView(dirname(__FILE__) . '/ueditor_view');
	}
}
		