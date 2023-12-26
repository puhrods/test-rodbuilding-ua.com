<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include_once DIR_SYSTEM . "library/client_translate_expert_core.php";
class LibraryClientTranslateExpert extends LibraryClientTranslateExpertCore
{
    const API_URL = "https://translator.codeguild.com.ua/en-gb/index.php?route=extension/module/server_translate_expert/";
    const ENCRIPTION_KEY = "yp6paWZ6v8%dB3JviNbCe48Lem8bFLnPRlhgsiNNxDMgBbY*!FpfWctVC!*ThR&ov^bqLzvVCt@H3WQHtCa8u!rcrSqLEGp4QL4";
    public function translate($text, $source, $target, $format)
    {
        $this->gebugLog("translate: ============START============", $startTime);
        $text = $this->trimValue($text);
        $this->gebugLog("\$text: " . var_export($text, true));
        list($source) = explode("-", $source);
        $this->gebugLog("\$source: " . var_export($source, true));
        list($target) = explode("-", $target);
        $this->gebugLog("\$target: " . var_export($target, true));
        $this->gebugLog("\$format: " . var_export($format, true));
        $res = $this->validateTranslateInternal();
        $this->gebugLog(var_export($res, true));
        if (is_null($res)) {
            $res = $this->translateInternal($this->_model->config->get($this->_keyParamName), $text, $source, $target, $format);
            $this->gebugLog(var_export($res, true));
        }
        if (isset($res->text)) {
            $this->gebugLog("\$res->text: " . var_export($res->text, true));
        }
        if (isset($res->texts) && 0 < count($res->texts)) {
            $this->gebugLog("\$res->texts[0]: " . var_export($res->texts[0], true));
        }
        $this->gebugLog(var_export($res, true));
        $this->gebugLog("translate: ============FINISH============", $startTime);
        return $res;
    }
    public function validateTranslateSiteInfo()
    {
        $this->gebugLog("validateTranslateSiteInfo: ============START============", $startTime);
        $keyFile = DIR_CACHE . "client_translate_expert.key";
        if (file_exists($keyFile)) {
            $keyContentEncrypted = file_get_contents($keyFile);
            $keyContent = $this->keyDecript($keyContentEncrypted);
            if ($this->checkKey($keyContent)) {
                $this->gebugLog("validateTranslateSiteInfo local key file is valid: ============FINISH============", $startTime);
                return (object) ["active" => 1, "licensed" => 1];
            }
            unlink($keyFile);
        }
        $siteInfo = $this->getSiteInfo();
        if (isset($siteInfo->licensed) && $siteInfo->licensed) {
            $keyContent = $this->generateKeyContent();
            $keyContentEncrypted = $this->keyEncript($keyContent);
            file_put_contents($keyFile, $keyContentEncrypted);
        }
        $this->gebugLog("validateTranslateSiteInfoend: ============FINISH============", $startTime);
        return $siteInfo;
    }
    protected function validateTranslateInternal()
    {
        // return;
        // $this->gebugLog("validateTranslateInternal: ============START============", $startTime);
        // $siteInfo = $this->validateTranslateSiteInfo();
        // if (!is_object($siteInfo) || !$siteInfo->active) {
        //     $this->gebugLog("validateTranslateInternal: ============SiteInfo ERROR============");
        //     $this->gebugLog("\$siteInfo: " . var_export($siteInfo, true));
        //     $this->loadLanguage($this->_modulePrefix . "/client_translate_expert");
        //     return (object) ["success" => 0, "error_code" => 2001, "message" => "Internal error.", "additional_message" => $this->_model->language->get("error_unlicensed")];
        // }
        // $this->gebugLog("validateTranslateInternal end: ============FINISH============", $startTime);
    }
    protected function generateKeyContent()
    {
        $site = $this->getSite();
        $res = $this->getRandomString(rand(100, 200)) . "<!~" . $site . "~!>" . $this->getRandomString(rand(100, 200));
        return $res;
    }
    protected function checkKey($siteKey)
    {
        $site = $this->getSite();
        $arr = explode("<!~" . $site . "~!>", $siteKey);
        return count($arr) == 2;
    }
    protected function keyDecript($encriptedText)
    {
        $c = base64_decode($encriptedText);
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, "yp6paWZ6v8%dB3JviNbCe48Lem8bFLnPRlhgsiNNxDMgBbY*!FpfWctVC!*ThR&ov^bqLzvVCt@H3WQHtCa8u!rcrSqLEGp4QL4", $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac("sha256", $ciphertext_raw, "yp6paWZ6v8%dB3JviNbCe48Lem8bFLnPRlhgsiNNxDMgBbY*!FpfWctVC!*ThR&ov^bqLzvVCt@H3WQHtCa8u!rcrSqLEGp4QL4", $as_binary = true);
        if (hash_equals($hmac, $calcmac)) {
            return $original_plaintext;
        }
    }
    protected function keyEncript($plainText)
    {
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plainText, $cipher, "yp6paWZ6v8%dB3JviNbCe48Lem8bFLnPRlhgsiNNxDMgBbY*!FpfWctVC!*ThR&ov^bqLzvVCt@H3WQHtCa8u!rcrSqLEGp4QL4", $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac("sha256", $ciphertext_raw, "yp6paWZ6v8%dB3JviNbCe48Lem8bFLnPRlhgsiNNxDMgBbY*!FpfWctVC!*ThR&ov^bqLzvVCt@H3WQHtCa8u!rcrSqLEGp4QL4", $as_binary = true);
        $encriptedText = base64_encode($iv . $hmac . $ciphertext_raw);
        return $encriptedText;
    }
    protected function getSiteInfo()
    {
        $site = $this->getSite();
        $url = "https://translator.codeguild.com.ua/en-gb/index.php?route=extension/module/server_translate_expert/getSiteInfo&site=" . urlencode($site);
        $response_str = $this->getRequest($url);
        $siteInfo = json_decode($response_str);
        return $siteInfo;
    }
    protected function getSite()
    {
        return HTTPS_CATALOG !== NULL ? HTTPS_CATALOG : HTTP_CATALOG;
    }
    public function translateTable($languageIdFrom, $mode, $tableName, $textColumnNameParam, $langIdToParam, $product_status, $product_quantity, $product_category_id, $stock_status)
    {
        $this->gebugLog("translateTable: ============START============", $startTime);
        if (is_null($mode)) {
            $mode = "both";
        }
        $languages = $this->getLanguages();
        $tableResult = $this->getTableToTranslateAnalizationInfo($languageIdFrom, $tableName, $languages, $mode, $langIdToParam, $product_status, $product_quantity, $product_category_id, $stock_status, false);
        $translatedCharCount = 0;
        $done = true;
        $start = microtime(true);
        if ($tableResult->tableTranslationIsSuppported) {
            foreach ($tableResult->textColumnNames as $textColumnName) {
                if (!($textColumnNameParam && $textColumnNameParam != $textColumnName)) {
                    foreach ($languages as $code => $language) {
                        if (!($langIdToParam && $langIdToParam != $language["language_id"])) {
                            $textsToTranslateLenght = 0;
                            $textRowsToTranslate = [];
                            $limit = self::TRANSLATION_ANALITIC_SQL_LIMIT;
                            $offset = 0;
                            while (true) {
                                $textToTranslateRows = $this->getTextToTranslateRows($languageIdFrom, $tableResult->table, $tableResult->pkColumnNames, $textColumnName, $mode, $langIdToParam, $product_status, $product_quantity, $product_category_id, $stock_status, $limit, $offset);
                                if (count($textToTranslateRows) != 0) {
                                    $offset += count($textToTranslateRows);
                                    foreach ($textToTranslateRows as $textToTranslateRow) {
                                        if ($textToTranslateRow["language_id_to"] == $language["language_id"]) {
                                            $textToTranslate = $textToTranslateRow["text_from"];
                                            if (0 < $textsToTranslateLenght && 5000 < $textsToTranslateLenght + mb_strlen($textToTranslate)) {
                                                $translateRes = $this->translateTableTexts($tableResult, $mode, $textColumnName, $product_status, $product_quantity, $product_category_id, $stock_status, $textRowsToTranslate);
                                                if (!$translateRes->success) {
                                                    return (object) ["success" => 0, "done" => 0, "message" => $translateRes->message, "additional_message" => isset($translateRes->additional_message) ? $translateRes->additional_message : ""];
                                                }
                                                $offset = 0;
                                                $translatedCharCount += $textsToTranslateLenght;
                                                $textsToTranslateLenght = 0;
                                                $textRowsToTranslate = [];
                                            }
                                            $textsToTranslateLenght += mb_strlen($textToTranslate);
                                            $textRowsToTranslate[] = $textToTranslateRow;
                                        }
                                    }
                                    if (0 < $translatedCharCount) {
                                        $time_elapsed_secs = microtime(true) - $start;
                                        if (10 < $time_elapsed_secs) {
                                            $done = false;
                                        }
                                    }
                                }
                            }
                            if (0 < $textsToTranslateLenght) {
                                $translateRes = $this->translateTableTexts($tableResult, $mode, $textColumnName, $product_status, $product_quantity, $product_category_id, $stock_status, $textRowsToTranslate);
                                if (!$translateRes->success) {
                                    return (object) ["success" => 0, "done" => 0, "message" => $translateRes->message, "additional_message" => isset($translateRes->additional_message) ? $translateRes->additional_message : ""];
                                }
                                $translatedCharCount += $textsToTranslateLenght;
                                $time_elapsed_secs = microtime(true) - $start;
                                if (10 < $time_elapsed_secs) {
                                    $done = false;
                                }
                                if (!$done) {
                                }
                            }
                            if ($done) {
                            }
                        }
                    }
                }
            }
        }
        $this->gebugLog("translateTable: ============FINISH============", $startTime);
        return (object) ["success" => 1, "done" => $done, "translatedCharCount" => $translatedCharCount];
    }
    protected function translateTableTexts($tableResult, $mode, $textColumnName, $product_status, $product_quantity, $product_category_id, $stock_status, $textRowsToTranslate)
    {
        $textsToTranslate = [];
        foreach ($textRowsToTranslate as $textToTranslateRow) {
            $textsToTranslate[] = $textToTranslateRow["text_from"];
        }
        $textsToTranslateOriginal = $textsToTranslate;
        $isCode = $this->arrayContainsSubstr("</", $textsToTranslate) || $this->arrayContainsSubstr("&lt;", $textsToTranslate) || $this->arrayContainsSubstr("&amp;", $textsToTranslate);
        $format = $isCode ? "html" : "text";
        if ($isCode) {
            $textsToTranslate = $this->decodeHtml($textsToTranslate);
        }
        $source = $textRowsToTranslate[0]["language_code_from"];
        $target = $textRowsToTranslate[0]["language_code_to"];
        $translationResults = $this->translate($textsToTranslate, $source, $target, $format);
        if (!$translationResults->success) {
            return $translationResults;
        }
        $c1 = count($translationResults->texts);
        $c2 = count($textRowsToTranslate);
        if ($c1 != $c2) {
            $this->gebugLog("count(" . $translationResults->texts . ") != count(" . $textRowsToTranslate . "), " . $c1 . " != " . $c2);
            $str = var_export($translationResults->texts, true);
            $this->gebugLog("translationResults->texts: " . $str);
            $str = var_export($textsToTranslate, true);
            $this->gebugLog("textsToTranslate: " . $str);
            $str = var_export($textRowsToTranslate, true);
            $this->gebugLog("textRowsToTranslate: " . $str);
            return (object) ["success" => 0, "error_code" => 1000, "message" => "Internal error.", "additional_message" => "count(" . $translationResults->texts . ") != count(" . $textRowsToTranslate . "), " . $c1 . " != " . $c2];
        }
        if ($isCode) {
            $translationResults->texts = $this->encodeHtml($translationResults->texts, $textsToTranslateOriginal);
        }
        $c1 = count($translationResults->texts);
        $c2 = count($textRowsToTranslate);
        if ($c1 != $c2) {
            $this->gebugLog("count(" . $translationResults->texts . ") != count(" . $textRowsToTranslate . "), " . $c1 . " != " . $c2);
            $str = var_export($translationResults->texts, true);
            $this->gebugLog("translationResults->texts: " . $str);
            $str = var_export($textsToTranslate, true);
            $this->gebugLog("textsToTranslate: " . $str);
            $str = var_export($textRowsToTranslate, true);
            $this->gebugLog("textRowsToTranslate: " . $str);
            return (object) ["success" => 0, "error_code" => 1000, "message" => "Internal error.", "additional_message" => "count(" . $translationResults->texts . ") != count(" . $textRowsToTranslate . "), " . $c1 . " != " . $c2];
        }
        for ($i = 0; $i < count($textRowsToTranslate); $i++) {
            $textToTranslateRow = $textRowsToTranslate[$i];
            $textToTranslate = $textToTranslateRow["text_from"];
            $translatedText = $translationResults->texts[$i];
            if (trim(strtolower($textToTranslate)) == trim(strtolower($translatedText)) || isset($textToTranslateRow["text_to"]) && trim(strtolower($textToTranslateRow["text_to"])) == trim(strtolower($translatedText))) {
                $this->insertSameTranslatedText($textToTranslateRow["language_id_from"], $textToTranslateRow["language_id_to"], $translatedText);
            }
            $this->insertTranslatedTableTextValue($tableResult, $mode, $textColumnName, $product_status, $product_quantity, $product_category_id, $stock_status, $textToTranslateRow, $translatedText);
        }
        return (object) ["success" => "1"];
    }
    protected function getTableToTranslateAnalizationInfo($languageIdFrom, $table, $languages, $mode = NULL, $langIdToParam = NULL, $product_status = false, $product_quantity = false, $product_category_id = NULL, $stock_status = NULL, $calcCounts = true, $continueAfterColumn = NULL, $offset = 0)
    {
        $columns = $this->getColumns($table);
        $langColumn = false;
        foreach ($columns as $column) {
            if ($column["column_name"] == "language_id") {
                $langColumn = $column;
            }
        }
        $columnNames = [];
        $pkColumnNames = [];
        $pkRealColumnNames = [];
        $pkRealToIgnoreColumnNames = [];
        $textColumnNames = [];
        $columnInfos = [];
        foreach ($columns as $column) {
            $columnIsText = $column["data_type"] == "varchar" || $column["data_type"] == "text" || strpos($column["data_type"], "text") !== false;
            $columnKey = is_null($column["column_key"]) ? "" : $column["column_key"];
            if ($columnKey == "PRI") {
                if ($column["extra"] == "auto_increment") {
                    $pkRealToIgnoreColumnNames[] = $column["column_name"];
                } else {
                    $pkRealColumnNames[] = $column["column_name"];
                }
            }
            if ($columnKey != "PRI" || !is_null($langColumn) && $columnKey == "PRI") {
                $columnNames[] = $column["column_name"];
                if ($columnKey == "PRI" && $column["column_name"] != "language_id") {
                    $pkColumnNames[] = $column["column_name"];
                }
                if ($columnIsText) {
                    $textColumnNames[] = $column["column_name"];
                }
                $priStr = $columnKey == "PRI" ? "-PRI" : "";
                $aiStr = $column["extra"] == "auto_increment" ? "-AI" : "";
                $columnInfos[] = $column["column_name"] . "(" . $column["data_type"] . $priStr . $aiStr . ")";
            }
        }
        if (count($pkColumnNames) == 0) {
            $pkColumnNames = array_slice($columnNames, 0, array_search("language_id", $columnNames));
        }
        $start = microtime(true);
        $tableCharCount = 0;
        $tableLanguageCharCounts = $this->initLanguageCharCountsArray($languages);
        $tableFieldCharCounts = [];
        $tableFieldLanguageCharCounts = [];
        $tableTranslationIsSuppported = count($pkRealColumnNames) != 1 && count($pkRealToIgnoreColumnNames) == 0;
        $lastProcessedColumn = $continueAfterColumn;
        $lastProcessedOffset = $offset;
        $isBreaked = false;
        if (array_search("language_id", $columnNames) !== false && count($pkColumnNames) != 0 && $tableTranslationIsSuppported) {
            foreach ($textColumnNames as $textColumnName) {
                $tableFieldCharCounts[$textColumnName] = 0;
                $tableFieldLanguageCharCounts[$textColumnName] = $this->initLanguageCharCountsArray($languages);
                if ($calcCounts) {
                    if (!is_null($continueAfterColumn)) {
                        if ($continueAfterColumn == $textColumnName) {
                            $continueAfterColumn = NULL;
                        }
                    } else {
                        $limit = self::TRANSLATION_ANALITIC_SQL_LIMIT;
                        $nextExists = true;
                        while ($nextExists) {
                            $textToTranslateRows = $this->getTextToTranslateRows($languageIdFrom, $table, $pkColumnNames, $textColumnName, $mode, $langIdToParam, $product_status, $product_quantity, $product_category_id, $stock_status, $limit + 1, $offset);
                            if (count($textToTranslateRows) != 0) {
                                $nextExists = count($textToTranslateRows) == $limit + 1;
                                $textToTranslateRows = array_slice($textToTranslateRows, 0, $limit);
                                $offset += count($textToTranslateRows);
                                $lastProcessedOffset = $offset;
                                foreach ($textToTranslateRows as $textToTranslateRow) {
                                    $langIdTo = $textToTranslateRow["language_id_to"];
                                    $charCount = mb_strlen($textToTranslateRow["text_from"]);
                                    $tableCharCount += $charCount;
                                    $tableLanguageCharCounts[$langIdTo]["char_count"] += $charCount;
                                    $tableFieldCharCounts[$textColumnName] += $charCount;
                                    $tableFieldLanguageCharCounts[$textColumnName][$langIdTo]["char_count"] += $charCount;
                                }
                                $time_elapsed_secs = microtime(true) - $start;
                                if (1 < $time_elapsed_secs && 0 < $tableCharCount) {
                                    $isBreaked = true;
                                }
                            }
                        }
                        if ($isBreaked) {
                        } else {
                            $lastProcessedColumn = $textColumnName;
                            $lastProcessedOffset = 0;
                            $offset = 0;
                        }
                    }
                }
            }
        }
        if (!$isBreaked) {
            $lastProcessedColumn = NULL;
            $lastProcessedOffset = 0;
        }
        return (object) ["table" => $table, "columns" => $columns, "columnNames" => $columnNames, "pkColumnNames" => $pkColumnNames, "pkRealColumnNames" => $pkRealColumnNames, "pkRealToIgnoreColumnNames" => $pkRealToIgnoreColumnNames, "textColumnNames" => $textColumnNames, "columnInfos" => $columnInfos, "tableTranslationIsSuppported" => $tableTranslationIsSuppported, "done" => !$isBreaked, "tableCharCount" => $tableCharCount, "tableLanguageCharCounts" => $tableLanguageCharCounts, "tableFieldCharCounts" => $tableFieldCharCounts, "tableFieldLanguageCharCounts" => $tableFieldLanguageCharCounts, "lastProcessedColumn" => $lastProcessedColumn, "lastProcessedOffset" => $lastProcessedOffset];
    }
    protected function getTablesToTranslateAnalizationInfo($languageIdFrom, $languageIdTo, $mode = NULL, $filterTable = "", $product_status = false, $product_quantity = false, $product_category_id = NULL, $stock_status = NULL, $continueAfterTable = NULL, $continueAfterColumn = NULL, $continueOffset = 0)
    {
        $start = microtime(true);
        if (is_null($mode)) {
            $mode = "both";
        }
        $tables = $filterTable ? [$filterTable] : $this->getTables();
        $languages = $this->getLanguages();
        $globalCharCount = 0;
        $globalLanguageCharCounts = $this->initLanguageCharCountsArray($languages);
        $lastProcessedTable = $continueAfterTable;
        $lastProcessedColumn = $continueAfterColumn;
        $lastProcessedOffset = $continueOffset;
        $done = true;
        $tablesResult = [];
        foreach ($tables as $table) {
            if (!is_null($continueAfterTable)) {
                if ($continueAfterTable == $table) {
                    $continueAfterTable = NULL;
                }
            } else {
                if (0 < $globalCharCount) {
                    $time_elapsed_secs = microtime(true) - $start;
                    if (1 < $time_elapsed_secs) {
                        $done = false;
                    }
                    if ($done && $lastProcessedTable == $tables[count($tables) - 1]) {
                        $lastProcessedTable = NULL;
                        $lastProcessedColumn = NULL;
                        $lastProcessedOffset = 0;
                    }
                    if (is_null($start)) {
                        $start = microtime(true);
                    }
                    $time_elapsed_secs = microtime(true) - $start;
                    $time_elapsed_secs = number_format((double) $time_elapsed_secs, 4, ".", "");
                    return (object) ["success" => 1, "done" => $done, "globalCharCount" => $globalCharCount, "globalLanguageCharCounts" => $globalLanguageCharCounts, "tablesResult" => $tablesResult, "tables" => $tables, "timeInSec" => $time_elapsed_secs, "lastProcessedTable" => $lastProcessedTable, "lastProcessedColumn" => $lastProcessedColumn, "lastProcessedOffset" => $lastProcessedOffset];
                }
                $tableResult = $this->getTableToTranslateAnalizationInfo($languageIdFrom, $table, $languages, $mode, $languageIdTo, $product_status, $product_quantity, $product_category_id, $stock_status, true, $continueAfterColumn, $continueOffset);
                $continueAfterColumn = NULL;
                $continueOffset = 0;
                if (0 < $tableResult->tableCharCount || $filterTable) {
                    if ($tableResult->tableTranslationIsSuppported) {
                        foreach ($tableResult->textColumnNames as $textColumnName) {
                            if (isset($tableResult->tableFieldLanguageCharCounts[$textColumnName])) {
                                foreach ($tableResult->tableFieldLanguageCharCounts[$textColumnName] as $langIdTo => $value) {
                                    $charCount = $value["char_count"];
                                    $globalCharCount += $charCount;
                                    $globalLanguageCharCounts[$langIdTo]["char_count"] += $charCount;
                                }
                            }
                        }
                    }
                    $tablesResult[] = $tableResult;
                }
                $lastProcessedColumn = $tableResult->lastProcessedColumn;
                $lastProcessedOffset = $tableResult->lastProcessedOffset;
                $done = $tableResult->done;
                if (!(!$done || !is_null($lastProcessedColumn) || (int) $lastProcessedOffset !== 0)) {
                    $lastProcessedTable = $table;
                }
            }
        }
    }
    protected function getTextToTranslateRows($languageIdFrom, $tableName, $pkColumnNames, $textColumnName, $mode, $langIdToParam = NULL, $product_status = false, $product_quantity = false, $product_category_id = NULL, $stock_status = NULL, $limit, $offset, $text_from_filter = NULL)
    {
        $this->gebugLog("getTextToTranslateRows: ============START============", $startTime);
        if (is_null($langIdToParam)) {
            $res = [];
            $languages = $this->getLanguages();
            foreach ($languages as $code => $language) {
                $newRows = $this->getTextToTranslateRows($languageIdFrom, $tableName, $pkColumnNames, $textColumnName, $mode, $language["language_id"], $product_status, $product_quantity, $product_category_id, $stock_status, $limit, $offset);
                $index = count($res);
                foreach ($newRows as $number => $newRow) {
                    $res[$index++] = $newRow;
                }
                $this->gebugLog("\$res: " . var_export($res, true));
            }
            $this->gebugLog("getTextToTranslateRows: ============FINISH============", $startTime);
            return $res;
        } else {
            $def_language_id = (int) $this->_model->config->get("config_language_id");
            $joinPkConditions = [];
            foreach ($pkColumnNames as $pkColumnName) {
                if ($pkColumnName != $textColumnName) {
                    $joinPkConditions[] = "t2." . $pkColumnName . " = t." . $pkColumnName;
                }
            }
            if (count($joinPkConditions) == 0) {
                $joinPkConditions[] = "false";
            }
            $additionalInternalWhereCondition = count($joinPkConditions) == 0 ? "false" : "true";
            $tableSameTranslatedText = self::TRANSLATE_EXPERT_SAME_TRANSLATIONS;
            $whereConditions = [];
            if ($mode == "only_empty" || $mode == "both") {
                $whereConditions[] = "REPLACE(REPLACE(REPLACE(REPLACE(t2." . $textColumnName . ", ' ', ''), '\t', ''), '\n', ''), '\r', '') = '' or t2." . $textColumnName . " is null";
            }
            if ($mode == "same_value" || $mode == "both") {
                $whereConditions[] = "REPLACE(REPLACE(REPLACE(REPLACE(LOWER(t2." . $textColumnName . "), ' ', ''), '\t', ''), '\n', ''), '\r', '') = REPLACE(REPLACE(REPLACE(REPLACE(LOWER(t." . $textColumnName . "), ' ', ''), '\t', ''), '\n', ''), '\r', '')\r\n\t\t\t\tand stt1.text_hash_sum is null\r\n\t\t\t\tand stt2.text_hash_sum is null";
            }
            $l1WhereCondition = $languageIdFrom ? "(l1.language_id = " . $languageIdFrom . ")" : "1 = 1";
            $l2WhereCondition = $langIdToParam ? "l2.language_id = " . $langIdToParam : "1 = 1";
            $this->_model->db->query("SET SESSION group_concat_max_len = 1000000;");
            $groupBy = is_null($text_from_filter) ? "t2.language_id, t." . $textColumnName : "t." . implode(", t.", $pkColumnNames) . ", t2.language_id";
            $filterByTextFrom = is_null($text_from_filter) ? "" : " and (t." . $textColumnName . " = '" . $text_from_filter . "')";
            $tableHasProductId = $this->tableHasFIeld($tableName, "product_id");
            $productJoin = $tableHasProductId && ($product_status || $product_quantity || $stock_status) ? "join " . DB_PREFIX . "product p on p.product_id = t.product_id" . ($product_status ? " and p.status" : "") . ($product_quantity ? " and p.quantity > 0" : "") . ($stock_status ? " and p.stock_status_id = " . $stock_status : "") : "";
            $productCategoryJoin = $tableHasProductId && $product_category_id ? "join " . DB_PREFIX . "product_to_category ptc on ptc.product_id = t.product_id and ptc.category_id = " . $product_category_id : "";
            $sql = "\r\nselect\r\n\tt." . implode(", t.", $pkColumnNames) . ", \r\n\tt.language_id language_id_from,\r\n\tl1.code language_code_from,\r\n\tt." . $textColumnName . " text_from,\r\n\tl2.language_id language_id_to, \r\n\tl2.code language_code_to, \r\n\tl2.image language_image_to, \r\n\tt2." . $textColumnName . " text_to,\r\n\tcount(*) value_count\r\nfrom " . DB_PREFIX . "language l1 \r\njoin `" . $tableName . "` t on l1.language_id = t.language_id\r\n\tand " . $additionalInternalWhereCondition . "\r\n\tand REPLACE(REPLACE(REPLACE(REPLACE(t." . $textColumnName . ", ' ', ''), '\t', ''), '\n', ''), '\r', '') <> ''\r\n\tand REPLACE(REPLACE(REPLACE(REPLACE(t." . $textColumnName . ", ' ', ''), '\t', ''), '\n', ''), '\r', '') <> '0'\r\n\tand t." . $textColumnName . " is not null \r\n\tand not (t." . $textColumnName . " REGEXP '^-?[0-9]+\$')\r\njoin " . DB_PREFIX . "language l2 on l2.language_id <> t.language_id and (" . $l2WhereCondition . ")\r\nleft join " . $tableName . " t2 on " . implode(" and ", $joinPkConditions) . " \r\n\tand t2.language_id = l2.language_id\r\nleft join " . $tableSameTranslatedText . " stt1 on stt1.language_id_from = l1.language_id\r\n\tand stt1.language_id_to = l2.language_id\r\n\tand stt1.text_hash_sum = unhex(MD5(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(t." . $textColumnName . "), ' ', ''), '\t', ''), '\n', ''), '\r', '')))\r\nleft join " . $tableSameTranslatedText . " stt2 on stt2.language_id_from = l1.language_id\r\n\tand stt2.language_id_to = l2.language_id\r\n\tand stt2.text_hash_sum = unhex(MD5(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(t2." . $textColumnName . "), ' ', ''), '\t', ''), '\n', ''), '\r', '')))\r\n" . $productJoin . "\r\n" . $productCategoryJoin . "\r\nwhere " . $l1WhereCondition . " " . $filterByTextFrom . "\r\n\tand (" . implode(" or ", $whereConditions) . ")\r\ngroup by " . $groupBy . "\r\norder by t." . implode(", t.", $pkColumnNames) . ", t.language_id, t2.language_id\r\nlimit " . $limit . " offset " . $offset . "\r\n\t\t";
            $this->gebugLog($sql);
            $columns = $this->_model->db->query($sql)->rows;
            $this->gebugLog(var_export($columns, true));
            $this->gebugLog("getTextToTranslateRows: ============FINISH============", $startTime);
            return $columns;
        }
    }
    protected function getRequest($url)
    {
        $ch = curl_init();
        if ($ch === false) {
            throw new Exception("failed to initialize CURL");
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if ($result === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
        return $result;
    }
}

?>