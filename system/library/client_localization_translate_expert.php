<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

include_once DIR_SYSTEM . "library/client_translate_expert_core.php";
class LocalizationLibraryClientTranslateExpert extends LibraryClientTranslateExpertCore
{
    const ANALIZE_BULK_SIZE = 10;
    public function analizeLocalization($languageIdFrom, $languageIdTo, $nextFileIndex = 0)
    {
        $this->gebugLog("analizeLocalization: ============START============", $startTime);
        $this->loadLanguage($this->_modulePrefix . "/client_translate_expert");
        $this->_model->load->model("localisation/language");
        if ($languageIdFrom == $languageIdTo) {
            return (object) ["success" => 0, "error_code" => 3003, "message" => "Internal error.", "additional_message" => $this->_model->language->get("error_lang_from_and_lang_to_are_equal")];
        }
        $languageFrom = $this->_model->model_localisation_language->getLanguage($languageIdFrom);
        $languageTo = $this->_model->model_localisation_language->getLanguage($languageIdTo);
        $languageFromFolder = $this->getLanguageFolder($languageFrom);
        $languageToFolder = $this->getLanguageFolder($languageTo);
        if (!file_exists($languageFromFolder)) {
            return (object) ["success" => 0, "error_code" => 3001, "message" => "Internal error.", "additional_message" => $this->_model->language->get("error_lang_from_folder_is_not_exists") . " " . $languageFromFolder];
        }
        if (!file_exists($languageToFolder)) {
            return (object) ["success" => 0, "error_code" => 3002, "message" => "Internal error.", "additional_message" => $this->_model->language->get("error_lang_to_folder_is_not_exists") . " " . $languageToFolder];
        }
        $languageFromFiles = $this->getDirContents($languageFromFolder, ".php");
        $languageToFiles = $this->getDirContents($languageToFolder, ".php");
        $analize = [];
        foreach ($languageFromFiles as $path => $contentFrom) {
            $pathTo = $path;
            if (version_compare(VERSION, "2.2", "<")) {
                if ($pathTo == "/" . $languageFrom["filename"] . ".php") {
                    $pathTo = "/" . $languageTo["filename"] . ".php";
                }
            } else {
                if ($pathTo == "/" . $languageFrom["code"] . ".php") {
                    $pathTo = "/" . $languageTo["code"] . ".php";
                }
            }
            $contentTo = isset($languageToFiles[$pathTo]) ? $languageToFiles[$pathTo] : "";
            $analizeLocalizationFileResult = $this->analizeLocalizationFile($languageFrom, $languageTo, $contentFrom, $contentTo, $path, $pathTo);
            $analize[] = $analizeLocalizationFileResult;
        }
        $analizeHtml = $this->buildAnalizeHtml($analize);
        $analizeDetails = array_slice($analize, $nextFileIndex, 10);
        $analizeDetailsHtml = $this->buildAnalizeDetailedHtml($analizeDetails);
        $newNextFileIndex = $nextFileIndex + 10;
        $done = count($analize) <= $newNextFileIndex;
        $res = (object) ["success" => 1, "done" => $done, "analize" => $analizeDetails, "html" => $analizeHtml, "detailsHtml" => $analizeDetailsHtml, "nextFileIndex" => $newNextFileIndex];
        $this->gebugLog(var_export($res, true));
        $this->gebugLog("analizeLocalization: ============FINISH============", $startTime);
        return $res;
    }
    public function saveLocalizationFile($fileContent, $langId, $path)
    {
        $this->gebugLog("analizeLocalization: ============START============", $startTime);
        if (is_null($fileContent) || is_null($langId) || is_null($path) || !$fileContent || !$langId || !$path) {
            return (object) ["success" => 0, "error_code" => 3003, "message" => "Internal error.", "additional_message" => "saveLocalizationFile: emty parameters is not allowed"];
        }
        $this->loadLanguage($this->_modulePrefix . "/client_translate_expert");
        $this->_model->load->model("localisation/language");
        $language = $this->_model->model_localisation_language->getLanguage($langId);
        $languageFolder = $this->getLanguageFolder($language);
        $fullPath = $languageFolder . $path;
        $fullFolder = dirname($fullPath);
        if (!file_exists($fullFolder)) {
            mkdir($fullFolder, 511, true);
        }
        file_put_contents($fullPath, $fileContent);
        $res = (object) ["success" => 1];
        $this->gebugLog(var_export($res, true));
        $this->gebugLog("analizeLocalization: ============FINISH============", $startTime);
        return $res;
    }
    private function buildAnalizeHtml($analize)
    {
        $analizeTotals = $this->calcAnalizeTotals($analize);
        $entry_fileCount = $this->_model->language->get("entry_fileCount");
        $entry_needToTranslateFileCount = $this->_model->language->get("entry_needToTranslateFileCount");
        $entry_needToTranslateValueCount = $this->_model->language->get("entry_needToTranslateValueCount");
        $entry_needToTranslateCharCount = $this->_model->language->get("entry_needToTranslateCharCount");
        $entry_TranslateAll = $this->_model->language->get("entry_TranslateAll");
        $html = "";
        $html .= "<div class=\"container localization-analize-totals\">";
        $html .= "<div class='row localization-analize-total-row'>";
        $html .= "<div class='col-md-3'>" . $entry_fileCount . "</div>";
        $html .= "<div class='col-md-3'>" . $analizeTotals["fileCount"] . "</div>";
        $html .= "</div>";
        $html .= "<div class='row localization-analize-total-row'>";
        $html .= "<div class='col-md-3'>" . $entry_needToTranslateFileCount . "</div>";
        $html .= "<div class='col-md-3'>" . $analizeTotals["needToTranslateFileCount"] . "</div>";
        $html .= "</div>";
        $html .= "<div class='row localization-analize-total-row'>";
        $html .= "<div class='col-md-3'>" . $entry_needToTranslateValueCount . "</div>";
        $html .= "<div class='col-md-3'>" . $analizeTotals["needToTranslateValueCount"] . "</div>";
        $html .= "</div>";
        $html .= "<div class='row localization-analize-total-row'>";
        $html .= "<div class='col-md-3'>" . $entry_needToTranslateCharCount . "</div>";
        $html .= "<div class='col-md-3'>" . $analizeTotals["needToTranslateCharCount"] . "</div>";
        $html .= "</div>";
        if (0 < $analizeTotals["needToTranslateFileCount"]) {
            $html .= "<div class='row localization-analize-total-row'>";
            $html .= "<div class='col-md-3'><button type='button' class='translate_localization_all_button' title='" . $entry_TranslateAll . "' onclick='doTranslateLocalizationAllExpert()'><i class='fa fa-language'></i>" . $entry_TranslateAll . "</button></div>";
            $html .= "</div>";
            $html .= "";
        }
        $html .= "</div>";
        $html .= "<br><br>";
        $html .= "<div class=\"container localization-analize-table\">";
        $html .= "</div>";
        return $html;
    }
    private function buildAnalizeDetailedHtml($analize)
    {
        $toTranslateInfo = $this->_model->language->get("entry_localization_file_to_translate_info");
        $toTranslateInfoFullTranslated = $this->_model->language->get("entry_localization_file_to_translate_info_full_translated");
        $html = "";
        foreach ($analize as $analizeFile) {
            $contentFrom = $analizeFile["contentFrom"];
            $contentTo = $analizeFile["contentTo"];
            $langFrom = $analizeFile["languageFrom"];
            $langTo = $analizeFile["languageTo"];
            $langFromImgUrl = $this->getLangImgUrl($langFrom["code"]);
            $langToImgUrl = $this->getLangImgUrl($langTo["code"]);
            $toTranslate = $analizeFile["toTranslate"];
            $toTranslateCount = count($toTranslate);
            $contentFrom = $this->highlightToTranslate($contentFrom, $toTranslate);
            $displayPath = $analizeFile["pathFrom"];
            if ($analizeFile["pathFrom"] != $analizeFile["pathTo"]) {
                $displayPath = $analizeFile["pathFrom"] . " => " . $analizeFile["pathTo"];
            }
            $fileToTranslateInfo = $toTranslateCount == 0 ? str_replace("{path}", $displayPath, $toTranslateInfoFullTranslated) : ($fileToTranslateInfo = str_replace(["{path}", "{count}"], [$displayPath, $toTranslateCount], $toTranslateInfo));
            $contentTo = htmlentities($contentTo);
            $html .= "<div class='row localization-analize-row' data-path-to='" . $analizeFile["pathTo"] . "'>";
            $html .= "<div class='col-md-12 localization-analize-file-header'><div class='info' onclick='clickTranslateExpertLocalizationHeader(this)'>" . $fileToTranslateInfo . "</div></div>";
            $html .= "</div>";
            $html .= "<div class='row localization-analize-row info' style='display:none;'>";
            $html .= "<div class='col-md-6 localization-analize-file-toolbar from'>";
            $html .= "<img src='" . $langFromImgUrl . "' alt='" . $langFrom["name"] . "' title='" . $langFrom["name"] . "'>";
            $html .= "</div>";
            $html .= "<div class='col-md-6 localization-analize-file-toolbar to'>";
            $html .= "<img src='" . $langToImgUrl . "' alt='" . $langTo["name"] . "' title='" . $langTo["name"] . "'>";
            if (0 < $toTranslateCount) {
                $html .= "<button class='translate_localization_button' title='Translate' onclick='doTranslateLocalizationExpert(this); return false;'><i class='fa fa-language'></i> Translate</button>";
            }
            $html .= "<button class='save_translation_button' title='Save' onclick='doSaveLocalizationExpert(this); return false;'><i class='fa fa-save'></i> Save</button>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class='row localization-analize-row info' style='display:none;'>";
            $html .= "<div class='col-md-6 localization-analize-file from'><pre data-lang-from='" . $langFrom["language_id"] . "'>" . $contentFrom . "</pre></div>";
            $html .= "<div class='col-md-6 localization-analize-file to'><textarea data-lang-to='" . $langTo["language_id"] . "'>" . $contentTo . "</textarea></div>";
            $html .= "</div>";
            $html .= "<div class='row localization-analize-row info' style='display:none;'>";
            $html .= "</div>";
        }
        $html .= "</div>";
        return $html;
    }
    private function calcAnalizeTotals($analize)
    {
        $fileCount = count($analize);
        $needToTranslateFileCount = 0;
        $needToTranslateValueCount = 0;
        $needToTranslateCharCount = 0;
        foreach ($analize as $analizeFile) {
            $toTranslate = $analizeFile["toTranslate"];
            $toTranslateCount = count($toTranslate);
            if (0 < $toTranslateCount) {
                $needToTranslateFileCount++;
            }
            $needToTranslateValueCount += $toTranslateCount;
            foreach ($toTranslate as $key => $toTranslateLine) {
                $needToTranslateCharCount += mb_strlen($toTranslateLine["text"]);
            }
        }
        return ["fileCount" => $fileCount, "needToTranslateFileCount" => $needToTranslateFileCount, "needToTranslateValueCount" => $needToTranslateValueCount, "needToTranslateCharCount" => $needToTranslateCharCount];
    }
    private function highlightToTranslate($content, $toTranslate)
    {
        foreach ($toTranslate as $key => $toTranslateLine) {
            $content = str_replace($toTranslateLine["full_text"], "{begin_highlight_vh}" . $toTranslateLine["full_text"] . "{end_highlight_vh}", $content);
        }
        $content = htmlentities($content);
        $content = str_replace("{begin_highlight_vh}", "<b>", $content);
        $content = str_replace("{end_highlight_vh}", "</b>", $content);
        return $content;
    }
    public function analizeLocalizationFile($languageFrom, $languageTo, $contentFrom, $contentTo, $pathFrom, $pathTo)
    {
        $contentFrom = $this->parsePhpInclude($contentFrom);
        $contentTo = $this->parsePhpInclude($contentTo);
        $contentFromParsed = $this->parseLocalizationFile($contentFrom);
        $contentToParsed = $this->parseLocalizationFile($contentTo);
        $toTranslate = [];
        foreach ($contentFromParsed as $key => $valueFrom) {
            if (!isset($contentToParsed[$key])) {
                $toTranslate[$key] = $valueFrom;
            }
        }
        $res = ["languageFrom" => $languageFrom, "languageTo" => $languageTo, "pathFrom" => $pathFrom, "pathTo" => $pathTo, "toTranslate" => $toTranslate, "contentFrom" => $contentFrom, "contentTo" => $contentTo];
        return $res;
    }
    private function parsePhpInclude($content)
    {
        $regExp = "/(?:include|require_once)\\s*\\(\\s*DIR_LANGUAGE\\s*\\.\\s*['\\\"](.*?)['\\\"]\\s*\\)\\s*;/ms";
        if (preg_match_all($regExp, $content, $matches, PREG_SET_ORDER, 0) !== false) {
            foreach ($matches as $match) {
                $includePath = DIR_CATALOG . "language/" . $match[1];
                $includedContent = file_exists($includePath) ? file_get_contents($includePath) : "";
                $includedContent = $this->parsePhpInclude($includedContent);
                $localizationFile = $this->parseLocalizationFile($includedContent);
                $replaceTo = "";
                foreach ($localizationFile as $key => $value) {
                    $replaceTo .= $value["full_text"] . "\n";
                }
                $content = str_replace($match[0], $replaceTo, $content);
            }
        }
        return $content;
    }
    public function parseLocalizationFile($content)
    {
        $res = [];
        $regExp1 = "/\\\$\\_\\[['\\\"]([\\w\\_\\d]+)['\\\"]]\\s*?=\\s*?'((\\\\.|[^'])*)'\\s*?\\;/ms";
        if (preg_match_all($regExp1, $content, $matches, PREG_SET_ORDER, 0) !== false) {
            foreach ($matches as $match) {
                $res[$match[1]] = ["text" => $match[2], "full_text" => $match[0]];
            }
        }
        $regExp2 = "/\\\$\\_\\[['\\\"]([\\w\\_\\d]+)['\\\"]]\\s*?=\\s*?\\\"((\\\\.|[^\\\"])*)\\\"\\s*?\\;/ms";
        if (preg_match_all($regExp2, $content, $matches, PREG_SET_ORDER, 0) !== false) {
            foreach ($matches as $match) {
                $res[$match[1]] = ["text" => $match[2], "full_text" => $match[0]];
            }
        }
        return $res;
    }
}

?>