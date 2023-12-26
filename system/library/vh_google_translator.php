<?php
require 'google-cloud-translate/vendor/autoload.php';
use Google\Cloud\Translate\V2\TranslateClient;

class VHGoogleTranslator  {
	private $translator;
	private $googleApiKey;
	
	const GOOGLE_TRANSLATE_MAX_LENGTH = 50 * 1000;
	
	function __construct($googleApiKey) 
	{
		$this->googleApiKey = $googleApiKey;
		$this->translator = new TranslateClient([
				'key' => $this->googleApiKey
		]);
	}

	public function translate($text, $source = '', $target = 'en', $format = 'html') 
	{

		if ($source == 'ua') $source = 'uk';
		if ($target == 'ua') $target = 'uk';
		
		if ($source == 'rs') $source = 'sr';
		if ($target == 'rs') $target = 'sr';
		
		//var_dump($text); print '<br>';
		//$res = $text;
		$res = $this->translateInternal($text, [
			'source' => $source,
			'target' => $target,
			'format' => $format
		]);

		$res = $this->fixFormatParameters($text, $res);

		$data = (object)array('success' => 1, 'text' => $res);
		return $data;
	}

	public function translateBatch($texts, $source = '', $target = 'en', $format = 'html') 
	{

		if ($source == 'ua') $source = 'uk';
		if ($target == 'ua') $target = 'uk';
		
		if ($source == 'rs') $source = 'sr';
		if ($target == 'rs') $target = 'sr';

		$resultTexts = array();
		while ($texts && (count($texts) > 0))
		{
			$translateResults = $this->translateBatchInternal(array_slice($texts, 0, 100), [
				'source' => $source,
				'target' => $target,
				'format' => $format
			]);
			
			foreach ($translateResults as $translateResult)
				$resultTexts[] = $translateResult;
				
			$texts = array_slice($texts, 100);
		}

		$resultTexts = $this->fixFormatParameters($texts, $resultTexts);

		$data = (object)array('success' => 1, 'texts' => $resultTexts);
		return $data;
	}

	private function fixFormatParameters($text, $translatedText)
	{
		if (is_array($text))
		{
			if (count($text) != count($translatedText))
				return $translatedText;

			$res = array();
			$i = 0;
			foreach ($text as $t)
			{
				$res[] = $this->fixFormatParameters($t, $translatedText[$i]);
				$i++;
			}
			return $res;
		}

		$res = $translatedText;
		$formatParameters = array('d' => 'д', 's' => 'с');
		foreach ($formatParameters as $paramKey => $paramValue)
			$res = $this->fixFormatParameter($text, $res, $paramKey, $paramValue);
		return $res;
	}

	private function fixFormatParameter($text, $translatedText, $paramKey, $paramValue)
	{
		//$regExp1 = '/(%[\+\-]?[\+\-]?\d?\d?\d?\.?\d?\d?\d?\$?'.$paramKey.')[^a-zA-Z]/ms';
		$regExp1 = '/(%'.$paramKey.')[^a-zA-Z]/ms';
		if (preg_match_all($regExp1, $text, $matches, PREG_SET_ORDER, 0) !== false)
		{
			$regExp2 = '/(%\s*'.$paramValue.')[^a-zA-Z]/ms';
			foreach ($matches as $match) 
			{
				$translatedText = preg_replace($regExp2, $matches[1], $translatedText);
			}
		}

		return $translatedText;
	}

	private function existsEmpty($texts)
	{
		foreach ($texts as $text)
			if (!$text || !trim($text))
				return true;
		return false;
	}

	private function translateBatchInternal($texts, $options)
	{
		$sizeSum = $this->calcSizeSum($texts);
		$translateResults = array();
		if ($sizeSum > self::GOOGLE_TRANSLATE_MAX_LENGTH || $this->existsEmpty($texts))
		{
			foreach ($texts as $text)
			{
				if ($text && trim($text))
					$translateResults[] = $this->translateInternal($text, $options);
				else
					$translateResults[] = '';
			}
		}
		else
		{
			$batchResults = $this->translator->translateBatch($texts, $options);	
			foreach ($batchResults as $batchResult)
			{
				$translateResults[] = $batchResult['text'];
			}
		}
		return $translateResults;
	}

	private function translateInternal($text, $options)
	{
		$translatedResult = '';
		while ($text != '')
		{
			$countToTranslate = strlen($text);
			if ($countToTranslate > self::GOOGLE_TRANSLATE_MAX_LENGTH)
			{
				$countToTranslate = strrpos($text, '.', self::GOOGLE_TRANSLATE_MAX_LENGTH - strlen($text));
				if ($countToTranslate === false)
					$countToTranslate = strrpos($text, '>', self::GOOGLE_TRANSLATE_MAX_LENGTH - strlen($text));
				if ($countToTranslate === false)
					$countToTranslate = strrpos($text, ' ', self::GOOGLE_TRANSLATE_MAX_LENGTH - strlen($text));
				if ($countToTranslate === false)
					$countToTranslate = self::GOOGLE_TRANSLATE_MAX_LENGTH - 1;
			}			
			
			$textToTranslate = substr($text, 0, $countToTranslate + 1);

			$res = $this->translator->translate($textToTranslate, $options);
			//var_dump($res); print '<br>';
			$translatedResult .= $res['text'];	

			$text = substr($text, $countToTranslate + 1);
		}
		
		return $translatedResult;
	}

	private function calcSizeSum($texts)
	{
		$sum = 0;
		foreach ($texts as $text)
			$sum += strlen($text);
		return $sum;
	}
}
