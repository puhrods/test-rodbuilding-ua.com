<?php

class Morphy {

	private $instances = array();
	
	private $wrong_roots = array('ЧЕРН');

	public function getRoots($words, $lang) {

    $lang = $this->getLanguage($lang);

		if ($lang == 'unknown') {
			return $words;
		}

    $morphy = $this->getInstance($lang);

    $words = $this->prepareWords($words);

		$roots = array();

		try {
			foreach ($words as $word) {
				
        // fix some wrong roots
				foreach ($this->wrong_roots as $wrong_root) {
					if (0 === strpos($word, $wrong_root)) {
						$roots[] = $wrong_root;
						continue 2;
					}
				}

				$pseudo_root = $morphy->getPseudoRoot($word);

				if (is_array($pseudo_root) && isset($pseudo_root[0]) && !empty($pseudo_root[0])) {
					// $roots[] = $pseudo_root[0];
					// sometimes for English lenguage getPseudoRoot returned array with 2 elements: first - $word, second - root
					$roots[] = end($pseudo_root);
				} else {
					$roots[] = $word;
				}
			}
		} catch (phpMorphy_Exception $e) {
			die('Error occured while text processing: ' . $e->getMessage());
		} catch (Exception $e) {
      die($e->getMessage());
    }

		$roots = array_unique($roots);

		return $roots;
  }
  
  public function getWordWeight($word, $lang, $options) {

    $weights = array(1);

    $lang = $this->getLanguage($lang);

		if ($lang != 'ru_RU') {
			return 1;
		}

    $morphy = $this->getInstance($lang);

		try {

      $partsOfSpeech = $morphy->getPartOfSpeech(mb_strtoupper($word));

			if (empty($partsOfSpeech) || empty($options)) {
				return 1;
			}

      $morphyPartsOfSpeech = $this->getPartsOfSpeech();

      foreach($partsOfSpeech as $key => $partOfSpeech) {
        foreach($options as $optionPartOfSpeech => $coefficient) {          
          if (isset($morphyPartsOfSpeech[$optionPartOfSpeech]) && $morphyPartsOfSpeech[$optionPartOfSpeech] == $partOfSpeech) {
            if ($coefficient == 0) {
              return 0;
            }
            $weights[] = $coefficient;
          }
        }
      }
			
    } catch (phpMorphy_Exception $e) {
			die('Error occured while getting word weight: ' . $e->getMessage());
    } catch (Exception $e) {
      die($e->getMessage());
    }
    
    return max($weights);      

  }

  private function getLanguage($lang) {
		switch ($lang) {
			case 'ru':
			case 'ru_RU':	
				$lang = 'ru_RU';
				break;
			case 'en':
			case 'en_EN':	
				$lang = 'en_EN';
				break;
			case 'ua':
			case 'uk_UA':	
				$lang = 'uk_UA';
				break;
			default :
				$lang = 'unknown';
    }
    
    return $lang;
  }

  private function getInstance($lang) {

		if (isset($this->instances[$lang])) {
			$morphy = $this->instances[$lang];
		} else {
			require_once DIR_SYSTEM . '/library/phpmorphy/src/common.php';

			// set some options
			$opts = array(
				// storage type, follow types supported
				// PHPMORPHY_STORAGE_FILE - use file operations(fread, fseek) for dictionary access, this is very slow...
				// PHPMORPHY_STORAGE_SHM - load dictionary in shared memory(using shmop php extension), this is preferred mode
				// PHPMORPHY_STORAGE_MEM - load dict to memory each time when phpMorphy intialized, this useful when shmop ext. not activated. Speed same as for PHPMORPHY_STORAGE_SHM type
				'storage' => PHPMORPHY_STORAGE_FILE,
				// Enable prediction by suffix
				'predict_by_suffix' => true,
				// Enable prediction by prefix
				'predict_by_db' => true,
				// TODO: comment this
				//'graminfo_as_text' => true,
			);

			// Path to directory where dictionaries located
			$dir = DIR_SYSTEM . '/library/phpmorphy/dicts/' . $lang;

			// Create phpMorphy instance
			try {
				$morphy = new phpMorphy($dir, $lang, $opts);
			} catch (phpMorphy_Exception $e) {
				die('Error occured while creating phpMorphy instance: ' . PHP_EOL . $e);
			}

			$this->instances[$lang] = $morphy;
		}

    return $morphy;
  }

  private function prepareWords($words) {

		if (!is_array($words)) {
			preg_match_all('/[[:alnum:]]{3,}/isu', stripslashes($words), $matches);
			$words = array_unique($matches[0]);
		}

		foreach ($words as &$word) {
			$word = mb_strtoupper($word, 'UTF-8');
		}
    unset($word);
    
    return $words;
  }

  private function getPartsOfSpeech() {
    return array(
      'NOUN' => 'С',
      'ADJ_FULL' => 'П',
      'ADJ_SHORT' => 'КР_ПРИЛ',
      'INFINITIVE' => 'ИНФИНИТИВ',
      'VERB' => 'Г',
      'ADVERB_PARTICIPLE' => 'ДЕЕПРИЧАСТИЕ',
      'PARTICIPLE' => 'ПРИЧАСТИЕ',
      'PARTICIPLE_SHORT' => 'КР_ПРИЧАСТИЕ',
      'NUMERAL' => 'ЧИСЛ',
      'NUMERAL_P' => 'ЧИСЛ-П',
      'PRONOUN' => 'МС',
      'PRONOUN_PREDK' => 'МС-ПРЕДК',
      'PRONOUN_P' => 'МС-П',
      'ADV' => 'Н',
      'PREDK' => 'ПРЕДК',
      'PREP' => 'ПРЕДЛ',
      'CONJ' => 'СОЮЗ',
      'INTERJ' => 'МЕЖД',
      'PARTICLE' => 'ЧАСТ',
      'INP' => 'ВВОДН',
      'PHRASE' => 'ФРАЗ'
    );
  }
}