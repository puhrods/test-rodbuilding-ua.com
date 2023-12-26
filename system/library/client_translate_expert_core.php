<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
/*
*/

class LibraryClientTranslateExpertCore
{
	const TRANSLATE_EXPERT_SAME_TRANSLATIONS = DB_PREFIX. 'translate_expert_same_translations';
	const TRANSLATION_ANALITIC_SQL_LIMIT = 100;

	protected $_debug = true;
	protected $_debugLog = null;
	protected $_model = null;
	protected $_modulePrefix = null;
	protected $_languages = null;
	protected $_keyParamName = null;

	public function __construct($model, $debug = true)
	{
		$this->_model = $model;
		$this->_modulePrefix = 'extension/module';
		if (version_compare(VERSION, '2.3', '<'))
		{
			$this->_modulePrefix = 'module';
		}
		$this->_keyParamName = 'client_translate_expert_key';
		if (version_compare(VERSION, '3.0', '>='))
		{
			$this->_keyParamName = 'module_client_translate_expert_key';
		}
		
		$this->_debug = $debug;
	}
	
	// translation

	
	public function analizeTableDetail($languageIdFrom, $mode, $tableName, $textColumnNameParam, $langIdToParam, $product_status, $product_quantity, $product_category_id, $stock_status)
	{
		$this->gebugLog('analizeTableDetail: ============START============', $startTime);

		if (is_null($mode))
			$mode = 'both';
		
		$this->loadLanguage($this->_modulePrefix . '/client_translate_expert');
		
		$languages = $this->getLanguages();

		$tableResult = $this->getTableToTranslateAnalizationInfo($languageIdFrom, $tableName, $languages, $mode, $langIdToParam, $product_status, $product_quantity, $product_category_id, $stock_status, false);
		
		$translatedCharCount = 0;
		$done = true;		
		$textToTranslateRows = array();
		$currLanguage = null;
		
		$start = microtime(true);
		if ($tableResult->tableTranslationIsSuppported)
		{
			foreach($tableResult->textColumnNames as $textColumnName)
			{
				if ($textColumnNameParam && ($textColumnNameParam != $textColumnName))
					continue;					
				
				foreach($languages as $code => $language)
				{
					if ($langIdToParam && ($langIdToParam != $language['language_id']))
						continue;

					$currLanguage = $language;
					$textsToTranslateLenght = 0;
					$textRowsToTranslate = array();
					
					$limit = self::TRANSLATION_ANALITIC_SQL_LIMIT;
					$offset = 0;
					while (true)
					{
						$textToTranslateRows = $this->getTextToTranslateRows($languageIdFrom, $tableResult->table, $tableResult->pkColumnNames, $textColumnName, 
							$mode, $langIdToParam, $product_status, $product_quantity, $product_category_id, $stock_status, $limit, $offset);
						if (count($textToTranslateRows) == 0)
							break;						
						$offset += count($textToTranslateRows);
						
						break; // offset should be external
					}
				}
			}	
		}
		
		$result = array();
		$nameColClass = "col-xs-4 col-sm-4 col-md-4 col-lg-4";
		
		$result[] = "<div class='row'>";
		$result[] = "	<div class='col-xs-3 col-sm-3 col-md-3 col-lg-3'>{$this->_model->language->get('entity_table')}</div>";
		$result[] = "	<div class='col-xs-3 col-sm-3 col-md-3 col-lg-3'>{$this->_model->language->get('entity_column')}</div>";
		$result[] = "	<div class='col-xs-3 col-sm-3 col-md-3 col-lg-3'>{$this->_model->language->get('entry_analization_mode')}</div>";
		$result[] = "</div>";
		$result[] = "<div class='row'>";
		$result[] = "	<div class='col-xs-3 col-sm-3 col-md-3 col-lg-3'>{$tableName}</div>";
		$result[] = "	<div class='col-xs-3 col-sm-3 col-md-3 col-lg-3'>{$textColumnNameParam}</div>";
        $result[] = "   <div class='col-xs-3 col-sm-3 col-md-3 col-lg-3'>{$this->_model->language->get('value_analization_mode_'.$mode)}</div>";
		$result[] = "</div>";
		$result[] = "<br><br>";
		if (count($textToTranslateRows) > 0)
		{
			$imageHtmlTo = $this->buildHtmlToLanguageImage($langIdToParam, $currLanguage['code']);
			$imageHtmlFrom = $this->buildHtmlToLanguageImage($textToTranslateRows[0]['language_id_from'],
				$textToTranslateRows[0]['language_code_from']);
			$idColumnsCount = count($textToTranslateRows[0]) - 7;
			$idColumns = array_slice($textToTranslateRows[0], 0, $idColumnsCount);
			$countTmp = floor(12 / ($idColumnsCount + 2));
			$colClass = "col-xs-{$countTmp} col-sm-{$countTmp} col-md-{$countTmp} col-lg-{$countTmp}";
			$result[] = "<div class='row'>";
			foreach($idColumns as $key => $value)
				$result[] = "	<div class='{$colClass}'>{$key}</div>";
			$result[] = "	<div class='{$colClass}'>{$imageHtmlFrom}</div>";
			$result[] = "	<div class='{$colClass}'>{$imageHtmlTo}</div>";
			$result[] = "</div>";
			foreach($textToTranslateRows as $textToTranslateRow)
			{
				$idColumns = array_slice($textToTranslateRow, 0, $idColumnsCount);
				$result[] = "<div class='row'>";
				foreach($idColumns as $key => $value)
					$result[] = "	<div class='{$colClass}'>{$value}</div>";
				$result[] = "	<div class='{$colClass}'>{$textToTranslateRow['text_from']}</div>";
				$result[] = "	<div class='{$colClass}'>{$textToTranslateRow['text_to']}</div>";
				$result[] = "</div>";
			}
		}		
		
		$this->gebugLog('analizeTableDetail: ============FINISH============', $startTime);
		return (object)array(
			'success' => 1,
			'result' => implode('', $result),
		);
	}

	public function analize($languageIdFrom, $languageIdTo, $mode = null, $filterTable = '', $product_status = false, $product_quantity = false, $product_category_id = null, $stock_status = null, 
		$continueAfterTable = null, $continueAfterColumn = null, $continueOffset = 0)
	{
		$this->gebugLog('analize: ============START============', $startTime);
		
		$analizeInfo = $this->getTablesToTranslateAnalizationInfo($languageIdFrom, $languageIdTo, $mode, $filterTable, $product_status, $product_quantity, $product_category_id, $stock_status, $continueAfterTable, $continueAfterColumn, $continueOffset);
		
		$start = microtime(true);
		
		if (is_null($mode))
			$mode = 'both';
		
		$this->loadLanguage($this->_modulePrefix . '/client_translate_expert');
		
		$languages = $this->getLanguages();
		$nameColClass = "col-xs-4 col-sm-4 col-md-4 col-lg-4";
		$langColumnSize = intval((12 - 4) / (count($languages) + 1));
		$langColClass = "col-xs-{$langColumnSize} col-sm-{$langColumnSize} col-md-{$langColumnSize} col-lg-{$langColumnSize}";

		$result = array();
		foreach($analizeInfo->tablesResult as $tableResult)
		{
			$tableResultHeaderHTML = array('<br><br>');
			$tableResultColumns = array();
			
		    //$tableResultHeaderHTML[] = '<div>Primari key Columns: ' . implode(', ', $tableResult->pkRealColumnNames) . '</div>';
		    //$tableResultHeaderHTML[] = '<div>tableTranslationIsSuppported: ' . ($tableResult->tableTranslationIsSuppported ? 'Yes' : 'No') . '</div>';
			$tableResultHeaderHTML[] = "<div class='row'><div class='{$nameColClass}'>{$this->_model->language->get('entity_table')}</div></div>";
	
			$titleTable = str_replace(array('{table}'), array($tableResult->table), $this->_model->language->get('title_translate_table'));
			$tableButtonId = $tableResult->table . '_globalCharCount';
			$tableButton = $this->buildHtmlTranslateTableButton($languageIdFrom, $tableResult->table, $titleTable, $mode, $tableResult->tableCharCount, null, null, 
				$product_status, $product_quantity, $product_category_id, $stock_status, null, $tableButtonId);
			$tableResultHeaderHTML[] = str_replace(array('{table}'), array($tableResult->table), $this->_model->language->get('message_analize_table_head_info'));
			$tableResultHeaderHTML[count($tableResultHeaderHTML) - 1] = "
				<div class='{$nameColClass}'>{$tableResultHeaderHTML[count($tableResultHeaderHTML) - 1]}</div>
				<!--div class='{$langColClass}'>{$tableButton}</div-->";
			// $tableResultHeaderHTML[] = 'Columns: ' . implode(', ', $columnInfos);

			foreach($tableResult->tableLanguageCharCounts as $langIdTo => $value)
			{
				if ($languageIdTo != $langIdTo)
					continue;
				$tableLangButtonId = $tableResult->table . '_CharCount_' . $value['code'];
				$countStr = $this->buildHtmlTranslateTableButton($languageIdFrom, $tableResult->table, $titleTable, $mode, $value['char_count'], null, $langIdTo, 
					$product_status, $product_quantity, $product_category_id, $stock_status, $value, $tableLangButtonId);
				$tableResultHeaderHTML[count($tableResultHeaderHTML) - 1] .= "<div class='{$langColClass}'>{$countStr}</div>";
			}
			$tableResultHeaderHTML[count($tableResultHeaderHTML) - 1] = "<div class='row'>{$tableResultHeaderHTML[count($tableResultHeaderHTML) - 1]}</div>";
			$tableResultHeaderHTML = implode('', $tableResultHeaderHTML);
				
			foreach($tableResult->textColumnNames as $textColumnName)
			{
				if (isset($tableResult->tableFieldCharCounts[$textColumnName])
					&& $tableResult->tableFieldCharCounts[$textColumnName] > 0)
				{
					$tableResultColumnHTML = array();
					
					$titleTableColumn = str_replace(array('{table}', '{textColumnName}'), array($tableResult->table, $textColumnName), $this->_model->language->get('title_translate_table_column'));
					$columnButtonId = $tableResult->table . '_' . $textColumnName . '_globalCharCount';
					$columnCountStr = $this->buildHtmlTranslateTableButton($languageIdFrom, $tableResult->table, $titleTableColumn, $mode, $tableResult->tableFieldCharCounts[$textColumnName], $textColumnName, null,
						$product_status, $product_quantity, $product_category_id, $stock_status, null, $columnButtonId);
					$tableResultColumnHTML[] = str_replace(array('{table}', '{textColumnName}'), array($tableResult->table, $textColumnName), $this->_model->language->get('message_analize_field_head_info'));
					$tableResultColumnHTML[count($tableResultColumnHTML) - 1] = "
						<div class='{$nameColClass}'>{$tableResultColumnHTML[count($tableResultColumnHTML) - 1]}</div>
						<!--div class='{$langColClass}'>{$columnCountStr}</div-->";
					foreach($tableResult->tableFieldLanguageCharCounts[$textColumnName] as $langIdTo => $value)
					{	
						if ($languageIdTo != $langIdTo)
							continue;
						$columnLangButtonId = $tableResult->table . '_' . $textColumnName . '_CharCount_' . $value['code'];
						$countStr = $this->buildHtmlTranslateTableButton($languageIdFrom, $tableResult->table, $titleTableColumn, $mode, $value['char_count'], $textColumnName, $langIdTo, 
							$product_status, $product_quantity, $product_category_id, $stock_status, $value, $columnLangButtonId);
						$tableResultColumnHTML[count($tableResultColumnHTML) - 1] .= "<div class='{$langColClass}'>{$countStr}</div>";
					}	
					$tableResultColumnHTML[count($tableResultColumnHTML) - 1] = "<div class='row'>{$tableResultColumnHTML[count($tableResultColumnHTML) - 1]}</div>";
					$tableResultColumnHTML = implode('', $tableResultColumnHTML);
					$tableResultColumns[] = (object)array(
						'column' => $textColumnName,
						'html' => $tableResultColumnHTML,
						'columnCharCount' => $tableResult->tableFieldCharCounts[$textColumnName],
						'languageCharCounts' => $tableResult->tableFieldLanguageCharCounts[$textColumnName]
					);
				}
			}	
		
			$result[] = (object)array(
				'table' => $tableResult->table,
				'headerHTML' => $tableResultHeaderHTML,
				'languageCharCounts' => $tableResult->tableLanguageCharCounts,
				'tableCharCount' => $tableResult->tableCharCount,
				'columns' => $tableResultColumns,
			);
		}
		
		$timeInSecInfo = str_replace('{timeInSec}', $analizeInfo->timeInSec, $this->_model->language->get('entry_time_in_sec'));
		
		$titleAllTables = $this->_model->language->get('title_translate_all_tables');
		$analizeHeadInfo = $this->_model->language->get('message_analize_global_info');
		$analizeHeadInfo = "
			<div class='{$nameColClass}'>{$analizeHeadInfo}</div>
			<!--div class='{$langColClass}'>{$this->buildHtmlTranslateTableButton($languageIdFrom, 'all', $titleAllTables, $mode, $analizeInfo->globalCharCount, null, null, $product_status, $product_quantity, $product_category_id, $stock_status, null, 'globalCharCount')}</div-->";
		$languageHeaderColumns = '';
		foreach($analizeInfo->globalLanguageCharCounts as $langIdTo => $value)
		{
			if ($languageIdTo != $langIdTo)
				continue;
			$languageHeaderColumns .= "<div class='{$langColClass}'>" . $this->buildHtmlToLanguageImage($langIdTo, $value['code']) . "</div>";
			$analizeHeadInfo .= "<div class='{$langColClass}'>"
				. $this->buildHtmlTranslateTableButton($languageIdFrom, 'all', $titleAllTables, $mode, $value['char_count'], null, $langIdTo, $product_status, $product_quantity, $product_category_id, $stock_status, $value, "globalLangCharCount{$value['code']}")
				. "</div>";
		}
			
		$analizeHeadInfo = "
			<div class='row'>
				<div class='{$nameColClass}'>{$this->_model->language->get('entry_name')}</div>
				<!--div class='{$langColClass}'>{$this->_model->language->get('entry_summ')}</div-->
				{$languageHeaderColumns}
			</div>
			<br>
			<div class='row'>{$analizeHeadInfo}</div>";
			
		$this->gebugLog('analize: ============FINISH============', $startTime);

		return (object)array(
			'success' => $analizeInfo->success,
			'done' => $analizeInfo->done,
			'globalCharCount' => $analizeInfo->globalCharCount,
			'globalLanguageCharCounts' => $analizeInfo->globalLanguageCharCounts,
			'analizeHeadInfo' => $analizeHeadInfo,
			'result' => $result,
			'tables' => $analizeInfo->tablesResult,			
			'timeInSec' => $analizeInfo->timeInSec,
			'timeInSecInfo' => $timeInSecInfo,
			'lastProcessedTable' => $analizeInfo->lastProcessedTable,
			'lastProcessedColumn' => $analizeInfo->lastProcessedColumn,
			'lastProcessedOffset' => $analizeInfo->lastProcessedOffset
		);
	}
	
	protected function insertSameTranslatedText($languageIdFrom, $languageIdTo, $text)
	{
		$this->gebugLog('insertSameTranslatedText: ============START============', $startTime);

		$tableName = self::TRANSLATE_EXPERT_SAME_TRANSLATIONS;
		$sql = "
INSERT IGNORE INTO {$tableName} (language_id_from, language_id_to, text_hash_sum)
SELECT l.language_id_from, l.language_id_to, t.text_hash_sum
FROM (SELECT unhex(MD5(REPLACE(REPLACE(REPLACE(REPLACE(LOWER('{$this->_model->db->escape($text)}'), ' ', ''), '\t', ''), '\n', ''), '\r', ''))) text_hash_sum) t
INNER JOIN (
	SELECT 	'{$languageIdFrom}' language_id_from, '{$languageIdTo}' language_id_to
	UNION ALL
	SELECT 	'{$languageIdTo}' language_id_from, '{$languageIdFrom}' language_id_to
) l ON 1 = 1
		";
		
		$this->gebugLog($sql);
		
		$this->_model->db->query($sql);

		$this->gebugLog('insertSameTranslatedText: ============FINISH============', $startTime);
	}
	
	protected function insertTranslatedTableTextValue($tableResult, $mode, $textColumnName, $product_status, $product_quantity, $product_category_id, $stock_status, $textToTranslateRow, $translatedText)
	{
		$this->gebugLog('insertTranslatedTableTextValue: ============START============', $startTime);

		$translatedText = $this->_model->db->escape($translatedText);
		
		if ($textToTranslateRow['value_count'] == 1)
		{		
			$rowsToTranslate = array($textToTranslateRow);
		}
		else
		{
			$rowsToTranslate = $this->getTextToTranslateRows($textToTranslateRow['language_id_from'], $tableResult->table, $tableResult->pkColumnNames, $textColumnName, $mode, $textToTranslateRow['language_id_to'], 
				$product_status, $product_quantity, $product_category_id, $stock_status, 100500, 0, $this->_model->db->escape($textToTranslateRow['text_from']));
		}
		
		foreach ($rowsToTranslate as $textToTranslateRow)
		{			
			$insertFields = array('language_id', $textColumnName);
			$selectFields = array("'" . $textToTranslateRow['language_id_to'] . "'", "'" . $translatedText . "'");
			$conditions = array("language_id = '{$textToTranslateRow['language_id_from']}'");	
			$conditionsForUpdate = array("language_id = '{$textToTranslateRow['language_id_to']}'");	

			foreach($tableResult->columnNames as $column)
			{
				if ($column != 'language_id'
					&& $column != $textColumnName
					&& array_search($column, $tableResult->pkRealToIgnoreColumnNames) === false
				){
					$insertFields[] = '`' . $column . '`';
					$selectFields[] = '`' . $column . '`';
				}
			}
		
			$pks = (count($tableResult->pkRealToIgnoreColumnNames) > 0) ? $tableResult->pkRealToIgnoreColumnNames : $tableResult->pkColumnNames;
			foreach($pks as $column)
			{
				if ($column != 'language_id' && $column != $textColumnName)
				{				
					$conditions[] = "{$column} = '{$textToTranslateRow[$column]}'";
					$conditionsForUpdate[] = "{$column} = '{$textToTranslateRow[$column]}'";
				}
			}
			
			$insertFieldsStr = implode(", ", $insertFields);
			$selectFieldsStr = implode(", ", $selectFields);
			$conditionsStr = implode(" AND ", $conditions);
			$conditionsForUpdateStr = implode(" AND ", $conditionsForUpdate);		
					
			$sql = "
	SELECT 'exists'
	FROM `{$tableResult->table}`
	WHERE {$conditionsForUpdateStr}
			";
			$this->gebugLog($sql);
			$rows = $this->_model->db->query($sql)->rows;
			$exists = count($rows) > 0;
			if ($exists)
			{		
				$sql = "
	UPDATE {$tableResult->table}
	SET {$textColumnName} = '{$translatedText}'
	WHERE {$conditionsForUpdateStr}
				";
				$this->gebugLog($sql);
				$this->_model->db->query($sql);
			}
			else
			{
				$sql = "
	INSERT INTO {$tableResult->table} ({$insertFieldsStr}) 
	SELECT {$selectFieldsStr} FROM `{$tableResult->table}` WHERE {$conditionsStr} LIMIT 1
				";
				$this->gebugLog($sql);
				$this->_model->db->query($sql);
			}
		}

		$this->gebugLog('insertTranslatedTableTextValue: ============FINISH============', $startTime);
	}

	protected function buildHtmlToLanguageImage($langIdTo, $langCodeTo) {
		$title = "{$this->_model->language->get('title_not_translater_char_count')} {$langCodeTo} (id:{$langIdTo})";
		$langImgUrl = $this->getLangImgUrl($langCodeTo);
		return "<img src='{$langImgUrl}' alt='{$title}' title='{$title}'>";
	}

	protected function buildHtmlTranslateTableButton($langIdFrom, $tableName, $title, $mode, $charCount, $textColumnName = null, $langIdTo = null, 
		$product_status = false, $product_quantity = false, $product_category_id = null, $stock_status = null, $value = null, $countId = null)
	{
		$server = HTTP_SERVER;
		$customStyleAttr = "style='background-image: url({$server}view/image/translate_expert_client/translate-icon-32.png);'";
		
		if (!is_null($langIdTo))
		{
			$titleLangSuffix = str_replace(
				array('{langCode}'), 
				array($value['code']), 
				$this->_model->language->get('title_translate_to_lang_suffix')
			);
			$title = $title . $titleLangSuffix;
		}

		$confirmation = str_replace(array('{translateTableTitle}'), array($title), $this->_model->language->get('title_translate_table_confirmation'));
		$translateOnclick = 'doTranslateTableExpert("' . $langIdFrom . '", "' . $mode . '", "' . $tableName . '", "' . $textColumnName . '", "' . $langIdTo . '", "' . $product_status . '", "' . $product_quantity . '", "' . $product_category_id . '", "' . $stock_status . '", "' . $confirmation . '")';
		
		$aTabBegin = $aTabEnd = "";
		if (!is_null($textColumnName) && !is_null($langIdTo))
		{
			$detailOnclick = 'analizeTableDetail("' . $langIdFrom . '", "' . $mode . '", "' . $tableName . '", "' . $textColumnName . '", "' . $langIdTo . '", "' . $product_status . '", "' . $product_quantity . '", "' . $product_category_id . '", "' . $stock_status . '"); return false;';
			$aTabBegin = "<a onclick='{$detailOnclick}' href='#'>";
			$aTabEnd = "</a>";
		}

		$idAttr = is_null($countId) ? '' : "id={$countId}";
		
		return "{$aTabBegin}<strong {$idAttr} title='{$this->_model->language->get('title_not_translater_char_count')}'>{$charCount}</strong>{$aTabEnd} " .
			"<input type='button' class='translate_button' {$customStyleAttr} title='{$title}' onclick='{$translateOnclick}'>";
	}

	protected function initLanguageCharCountsArray($languages) {
		$res = array();
		foreach($languages as $code => $language)
			$res[$language['language_id']] = array('code' => $code, 'image' => isset($language['image']) ? $language['image'] : $code . '.png', 'char_count' => 0);
		return $res;
	}

	protected function getColumns($table) {		
		$this->gebugLog('getColumns: ============START============', $startTime);
		
		//$row = $this->_model->db->query("SELECT * FROM " . $table . " LIMIT 1")->row;
		$columns = $this->_model->db->query("
			SELECT column_name as 'column_name', data_type as 'data_type', column_key as 'column_key', extra as 'extra'
			from INFORMATION_SCHEMA.COLUMNS 
			where table_schema = '" . DB_DATABASE . "' 
				and table_name = '" . $table . "'
				and column_name <> 'image'
				and column_name <> 'link'
				and column_name <> 'query'
				and column_name <> 'url'
		")->rows;
		$this->gebugLog(var_export($columns, true));
        foreach($columns as &$column)
		{
			$column = array_change_key_case($column);
		}

		$this->gebugLog('getColumns: ============FINISH============', $startTime);
		return $columns;
	}

	public function getTables() {
		$this->gebugLog('getTables: ============START============', $startTime);
		
		$tableRows = $this->_model->db->query("
			select c.table_name as 'table_name' 
			from INFORMATION_SCHEMA.COLUMNS c
			where c.table_schema = '" . DB_DATABASE . "' 
				and c.column_name = 'language_id'
				and c.table_name like '" . DB_PREFIX . "%'
				and not c.table_name like '%-%'
				and not c.table_name like '%+%'
			order by c.table_name
		")->rows;
		$this->gebugLog(var_export($tableRows, true));
		$tables = array();
		$ignore = array(DB_PREFIX.'language', DB_PREFIX.'customer', DB_PREFIX.'customer_search', DB_PREFIX.'order', DB_PREFIX.'translation', DB_PREFIX.'seo_url');
        foreach($tableRows as $tableRow)
		{
			$tableRow = array_change_key_case($tableRow);
			if (strpos($tableRow['table_name'], 'url') !== false)
				continue;
			if (array_search($tableRow['table_name'], $ignore) === false)
				$tables[] = $tableRow['table_name'];	
		}

		$this->gebugLog('getTables: ============FINISH============', $startTime);
		return $tables;
	}

	public function tableHasFIeld($table, $field)
	{
		$this->gebugLog('tableHasFIeld: ============START============', $startTime);
		
		//$row = $this->_model->db->query("SELECT * FROM " . $table . " LIMIT 1")->row;
		$columns = $this->_model->db->query("
			SELECT 1
			from INFORMATION_SCHEMA.COLUMNS 
			where table_schema = '" . DB_DATABASE . "' 
				and table_name = '" . $table . "'
				and column_name = '" . $field . "'
		")->rows;

		$this->gebugLog('tableHasFIeld: ============FINISH============', $startTime);
		return count($columns) > 0;
	}
	// translation

	// REGION COMMON
	protected function translateInternal($googleApiKey, $text, $source, $target, $format) {
		
		$this->gebugLog('translateInternal: ============START============', $startTime);

		include_once(DIR_SYSTEM . 'library/vh_google_translator.php');
		
		$this->gebugLog('translateInternal: vh_google_translator');
		
		if (!$googleApiKey)
		{
			$res = (object)array('success' => 0, 'error_code' => 1004, 'message' => 'Google Translate API key is empty.');
			$this->gebugLog('translateInternal googleApiKey is empty: ============END============', $startTime);
			return $res;
		}

		try {
			//var_dump($text); print '<br>';
			$this->gebugLog('translateInternal VHGoogleTranslator creation before');
			$translator = new VHGoogleTranslator( $googleApiKey );
			$this->gebugLog('translateInternal VHGoogleTranslator creation after');
			$res = is_array($text)
				? $translator->translateBatch($text, $source, $target, $format)
				: $translator->translate($text, $source, $target, $format);
			$this->gebugLog(var_export($res, true));
			//var_dump($res); print '<br>';
		}
		catch (\Google\Cloud\Core\Exception\BadRequestException $exception) {
			//var_dump($exception);
			$this->gebugLog('Google\Cloud\Core\Exception\BadRequestException', $startTime);
			$errorMessage = $exception->getMessage();
			$this->gebugLog(var_export($errorMessage, true));
			$error = json_decode($errorMessage)->error;
			$res = (object)array('success' => 0, 'error_code' => 1000, 'message' => 'Internal error.', 'additional_message' => '(' . $error->code . ') ' . $error->message);
			$this->gebugLog(var_export($res, true));
			$this->gebugLog('translateInternal: ============END============', $startTime);
			return $res;
		}
		catch(\Exception $exception)
		{
			$error = $exception->getMessage();
			$res = (object)array('success' => 0, 'error_code' => 1000, 'message' => 'Internal error.', 'additional_message' => $error);
			$this->gebugLog(var_export($res, true));
			$this->gebugLog('translateInternal: ============END============', $startTime);
			return $res;
		}
		catch(\Throwable $exception)
		{
			$error = $exception->getMessage();
			$res = (object)array('success' => 0, 'error_code' => 1000, 'message' => 'Internal error.', 'additional_message' => $error);
			$this->gebugLog(var_export($res, true));
			$this->gebugLog('translateInternal: ============END============', $startTime);
			return $res;
		}

		$this->gebugLog(var_export($res, true));
		$this->gebugLog('translateInternal: ============END============', $startTime);
		return $res;
	}	
	// REGION COMMON
	
	
	// LOCALIZATION
	protected function getDirContents($dir, $pathSuffix, &$relativePath = '', &$results = array()) {
		$files = scandir($dir);

		foreach ($files as $key => $value)
		{
			$path = realpath($dir . '/' . $value);
			$relPath = $relativePath . '/' . $value;
			//var_dump($relPath);
			if (!is_dir($path))
			{
				if ($this->endsWith($path, $pathSuffix))
				{
					$results[$relPath] = file_get_contents($path);
				}
			}
			else
			if ($value != "." && $value != "..")
			{
				$this->getDirContents($path, $pathSuffix, $relPath, $results);
			}
		}

		return $results;
	}
	// LOCALIZATION	
	
	
	
	
	
	
	
	
	
	// common functions
	protected function trimValue($text)
	{
		if (is_array($text))
		{
			$newArr = array();
			foreach($text as $t)
			{				
				$t = trim($t);	
				if ($t && ($t != '') && !is_null($t)) {
					$newArr[] = $t;
				}
			}
			$text = $newArr;
		}
		else
		{
			$text = trim($text);
		}
		
		return $text;
	}
	
	function startsWith( $haystack, $needle ) {
		 $length = strlen( $needle );
		 return substr( $haystack, 0, $length ) === $needle;
	}

	function endsWith( $haystack, $needle ) {
		$length = strlen( $needle );
		if( !$length ) {
			return true;
		}
		return substr( $haystack, -$length ) === $needle;
	}
	
	protected function getRandomString($length = 8) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$string = '';

		for ($i = 0; $i < $length; $i++) {
			$string .= $characters[mt_rand(0, strlen($characters) - 1)];
		}

		return $string;
	}	
	
	protected function arrayContainsSubstr($substr, array $arr)
	{
		foreach($arr as $a) {
			if (mb_strpos($a, $substr, 0, "UTF-8") !== false)
				return true;
		}
		return false;
	}
	
	protected function decodeHtml($texts)
	{
		foreach($texts as &$text)
		{
			if (mb_strpos($text, '&lt;', 0, "UTF-8") !== false || mb_strpos($text, '&amp;', 0, "UTF-8") !== false)
			{
				$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
			}
		}
		return $texts;
	}
	
	protected function encodeHtml($texts, $originals)
	{
		foreach($originals as $key => &$original)
		{
			if (mb_strpos($original, '&lt;', 0, "UTF-8") !== false || mb_strpos($original, '&amp;', 0, "UTF-8") !== false)
			{
				$texts[$key] = htmlentities($texts[$key], ENT_QUOTES, 'UTF-8');
			}
		}
		return $texts;
	}
	
	protected function loadLanguage($module)
	{
		if (version_compare(VERSION, '2.1', '='))
			return $this->_model->language->load($module);
		else
			return $this->_model->load->language($module);
	}
	
	protected function getLangImgUrl($langCode)
	{
		if (version_compare(VERSION, '2.2', '<'))
			return 'view/image/flags/' . $this->getLangImageName($langCode);
		else
			return "language/{$langCode}/{$langCode}.png";
	}
	
	protected function getLangImageName($langCode)
	{
		foreach($this->getLanguages() as $key => $language)
		{
			if ($language['code'] == $langCode)
			{
				if (isset($language['image']))
					return $language['image'];
			}
		}
		
		return $langCode;
	}
	
	protected function getLanguageFolder($language)
	{
		if (version_compare(VERSION, '2.2', '<'))
			return DIR_CATALOG . 'language/' . $language['directory'] . '/';
		else
			return DIR_CATALOG . 'language/' . $language['code'] . '/';
	}
	
	protected function getLanguages()
	{
		if (is_null($this->_languages))
		{
			$this->_model->load->model('localisation/language');
			$this->_languages = $this->_model->model_localisation_language->getLanguages();
		}
		return $this->_languages;
	}
	
	public function gebugLog($message, &$startTime = null)
	{
		if (!$this->_debug)
			return;
		
		if (is_null($this->_debugLog))
			$this->_debugLog = new Log('client_translate_expert.log');
		
		$executionTimeStr = '';
		$currentTime = microtime(true);
		if (is_null($startTime) || !isset($startTime))
			$startTime = $currentTime;
		else
		{
			$secs = $currentTime - $startTime;
			$executionTimeStr = ' (' . number_format($secs, 3, '.', "`") . ' sec)';	
		}
		
		$currentDateTime = DateTime::createFromFormat('U.u', $currentTime);
		if (is_bool($currentDateTime)) $currentDateTime = DateTime::createFromFormat('U.u', $currentTime += 0.001);
		$this->_debugLog->write($currentDateTime->format("Y-m-d H:i:s.v") . $executionTimeStr . PHP_EOL
			. $message . PHP_EOL);
	}
	// common functions
	
	// installation
	public function install()
	{
		$sql = "
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "translate_expert_same_translations` (
		  `language_id_from` int(11) NOT NULL,
		  `language_id_to` int(11) NOT NULL,
		  `text_hash_sum` binary(16) NOT NULL,
		  PRIMARY KEY(`language_id_from`,`language_id_to`,`text_hash_sum`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT
		";
		return $this->_model->db->query($sql);
	}
	
	public function uninstall()
	{
		$sql = "DROP TABLE IF EXISTS `" . DB_PREFIX . "translate_expert_same_translations`";
		return $this->_model->db->query($sql);
	}
	// installation
}
