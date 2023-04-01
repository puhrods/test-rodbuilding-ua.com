<?php

namespace OCFilter;

class Helper extends Factory {  
  public function declOfNum($number, array $cases) {
    if ($number % 10 == 1 && $number % 100 != 11) {
      return sprintf($cases[0], $number);
    } else if ($number % 10 >= 2 && $number % 10 <= 4 && ($number % 100 < 10 || $number % 100 >= 20)) {
      return sprintf($cases[1], $number);
    } else {
      return sprintf($cases[2], $number);
    }
  }
  
  // https://stackoverflow.com/a/49122313
  public function number_abbr($number) {
    $abbrevs = [ 12 => 't', 9 => 'b', 6 => 'm', 3 => 'k', 0 => '' ];

    foreach ($abbrevs as $exponent => $abbrev) {
      if (abs($number) >= pow(10, $exponent)) {
        $display = $number / pow(10, $exponent);
        $decimals = ($exponent >= 3 && round($display) < 100 && abs($display - round($display)) > 0.1) ? 1 : 0;
        $number = number_format($display, $decimals) . ($abbrev ? '<span class="ocf-num-abbr">' . $abbrev . '</span>' : '');
        
        break;
      }
    }

    return $number;
  }  

  public function translit($string) {
    $replace = array(
      'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ґ' => 'g',
      'д' => 'd', 'е' => 'e', 'є' => 'je', 'ё' => 'e', 'ж' => 'zh',
      'з' => 'z', 'и' => 'i', 'і' => 'i', 'ї' => 'ji', 'й' => 'j',
      'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
      'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
      'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh',
      'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e',
      'ю' => 'ju', 'я' => 'ja',
      '+' => 'plus', '%' => 'prc',
    );

    $translit = utf8_strtolower($string);
    $translit = htmlspecialchars_decode($translit, ENT_QUOTES);
    $translit = strip_tags(html_entity_decode($translit, ENT_QUOTES, 'UTF-8'));

    $translit = strtr($translit, $replace);

    $translit = preg_replace('/[^a-z0-9\s,]/', '', $translit);
    $translit = preg_replace('/[,-]/', ' ', $translit);
    $translit = preg_replace('/\s+/', '-', $translit);
    $translit = preg_replace('/\-+/', '-', $translit);
    $translit = trim($translit, '-');

    return $translit;
  }

  public function utf8_ucfirst($string) {
    return utf8_strtoupper(utf8_substr($string, 0, 1)) . utf8_substr($string, 1);
  }
   
  public function findExecutable() {
    $finder = new PhpExecutableFinder();

    return $finder->find();
  }  
  
  public function getNumeralLocales() {
    return [
      "en",     "bg",     "chs",    "cs",     "da-dk",  "de-ch",  "de",
      "en-au",  "en-gb",  "en-za",  "es-es",  "es",     "et",
      "fi",     "fr-ca",  "fr-ch",  "fr",     "hu",     "it",     "ja",
      "lv",     "nl-be",  "nl-nl",  "no",     "pl",     "pt-br",  "pt-pt",
      "ru-ua",  "ru",     "sk",     "sl",     "th",     "tr",     "uk-ua", "vi",
    ];
  }  

  public function getRenderedStyle() {
    $DIR_ROOT = preg_replace('/(\/|\\\)catalog(\/|\\\).*$/i', '', DIR_APPLICATION) . '/';
       
    $css_core = $this->opencart->getThemeFile('stylesheet/ocfilter/core.css');
    $css_mobile = $this->opencart->getThemeFile('stylesheet/ocfilter/mobile.css');
    $css_desktop = $this->opencart->getThemeFile('stylesheet/ocfilter/desktop.css');
    
    if ($this->config('mobile_max_width')) {
      $width = (int)$this->config('mobile_max_width');
    } else {
      $width = 767;
    }

    $filemtime = 0;

    if (is_file($DIR_ROOT . $css_core)) {
      $filemtime += (int)filemtime($DIR_ROOT . $css_core);
    }

    if (is_file($DIR_ROOT . $css_mobile)) {
      $filemtime += (int)filemtime($DIR_ROOT . $css_mobile);
    }

    if (is_file($DIR_ROOT . $css_desktop)) {
      $filemtime += (int)filemtime($DIR_ROOT . $css_desktop);
    }   
  
    if (is_writable(dirname($css_core))) {
      $directory = dirname($css_core);
    } else {
      $directory = DIR_IMAGE . 'ocfilter';
      
      if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
      }
    }

    $file = $directory . '/' . sprintf('ocf.cache.%s.%s.css', $width, $filemtime);

    if (!is_file($file)) {
      // Clear old cache  
      if (($files = glob($directory . '/ocf.cache.*')) && count($files) >= 5) {
        foreach (array_slice(array_reverse($files), 4) as $old_file) {
          if (is_file($old_file)) {
            unlink($old_file);
          }
        }
      }

      // Build new css
      $css = '';

      if (is_file($DIR_ROOT . $css_core)) {    
        $css .= $this->minimize(file_get_contents($DIR_ROOT . $css_core)) . "\n";
      }

      if (is_file($DIR_ROOT . $css_mobile)) {
        $css .= '@media (max-width: ' . $width . 'px) {' . "\n" . $this->minimize(file_get_contents($DIR_ROOT . $css_mobile)) . "\n" . '}' . "\n";
      }

      if (is_file($DIR_ROOT . $css_desktop)) {
        $css .= '@media (min-width: ' . ($width + 1) . 'px) {' . "\n" . $this->minimize(file_get_contents($DIR_ROOT . $css_desktop)) . "\n" . '}' . "\n";
      }    
      
      file_put_contents($file, $css);   
    }
    
    return str_replace($DIR_ROOT, '', $file);    
  }
  
  // https://gist.github.com/Rodrigo54/93169db48194d470188f
  public function minimize($css) {
    return preg_replace(
      [
        // Remove comment(s)
        '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
        // Remove unused white-space(s)
        '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~]|\s(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
        // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
        '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
        // Replace `:0 0 0 0` with `:0`
        '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
        // Replace `background-position:0` with `background-position:0 0`
        '#(background-position):0(?=[;\}])#si',
        // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
        '#(?<=[\s:,\-])0+\.(\d+)#s',
        // Minify string value
        '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
        '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
        // Minify HEX color code
        '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
        // Replace `(border|outline):none` with `(border|outline):0`
        '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
        // Remove empty selector(s)
        '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
      ],
      [
        '$1',
        '$1$2$3$4$5$6$7',
        '$1',
        ':0',
        '$1:0 0',
        '.$1',
        '$1$3',
        '$1$2$4$5',
        '$1$2$3',
        '$1:0',
        '$1$2'
      ],
      $css);  
  }
}


/**
 * An executable finder specifically designed for the PHP executable.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class PhpExecutableFinder {
  private $executableFinder;
  
  public function __construct() {
    $this->executableFinder = new ExecutableFinder();
  }
  
  /**
   * Finds The PHP executable.
   *
   * @param bool $includeArgs Whether or not include command arguments
   *
   * @return string|false The PHP executable path or false if it cannot be found
   */
  public function find($includeArgs = true) {
    if ($php = getenv('PHP_BINARY')) {
      if (!is_executable($php)) {
        $command = '\\' === \DIRECTORY_SEPARATOR ? 'where' : 'command -v';
        
        if ($php = strtok(exec($command . ' ' . escapeshellarg($php)), PHP_EOL)) {
          if (!is_executable($php)) {
            return false;
          }
        } else {
          return false;
        }
      }
      
      return $php;
    }
    
    $args = $this->findArguments();
    $args = $includeArgs && $args ? ' ' . implode(' ', $args) : '';
    
    // PHP_BINARY return the current sapi executable
    if (PHP_BINARY && \in_array(\PHP_SAPI, array('cli', 'cli-server', 'phpdbg'), true)) {
      return PHP_BINARY . $args;
    }
    
    if ($php = getenv('PHP_PATH')) {
      if (!@is_executable($php)) {
        return false;
      }
      
      return $php;
    }
    
    if ($php = getenv('PHP_PEAR_PHP_BIN')) {
      if (@is_executable($php)) {
        return $php;
      }
    }
    
    if (@is_executable($php = PHP_BINDIR . ('\\' === \DIRECTORY_SEPARATOR ? '\\php.exe' : '/php'))) {
      return $php;
    }
    
    $dirs = array(PHP_BINDIR);
    
    if ('\\' === \DIRECTORY_SEPARATOR) {
      $dirs[] = 'C:\xampp\php\\';
    }
    
    return $this->executableFinder->find('php', false, $dirs);
  }
  /**
   * Finds the PHP executable arguments.
   *
   * @return array The PHP executable arguments
   */
  public function findArguments() {
    $arguments = array();
    
    if ('phpdbg' === \PHP_SAPI) {
      $arguments[] = '-qrr';
    }
    
    return $arguments;
  }
}

/**
 * Generic executable finder.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ExecutableFinder {
  private $suffixes = array('.exe', '.bat', '.cmd', '.com');
  
  /**
   * Replaces default suffixes of executable.
   */
  public function setSuffixes(array $suffixes) {
    $this->suffixes = $suffixes;
  }
  
  /**
   * Adds new possible suffix to check for executable.
   *
   * @param string $suffix
   */
  public function addSuffix($suffix) {
    $this->suffixes[] = $suffix;
  }
  
  /**
   * Finds an executable by name.
   *
   * @param string $name      The executable name (without the extension)
   * @param string $default   The default to return if no executable is found
   * @param array  $extraDirs Additional dirs to check into
   *
   * @return string The executable path or default value
   */
  public function find($name, $default = null, array $extraDirs = array()) {
    if (ini_get('open_basedir')) {
      $searchPath = explode(PATH_SEPARATOR, ini_get('open_basedir'));
      $dirs = array();
      
      foreach ($searchPath as $path) {
        // Silencing against https://bugs.php.net/69240
        if (@is_dir($path)) {
          $dirs[] = $path;
        } else {
          if (basename($path) == $name && @is_executable($path)) {
            return $path;
          }
        }
      }
    } else {
      $dirs = array_merge(explode(PATH_SEPARATOR, getenv('PATH') ? : getenv('Path')), $extraDirs);
    }
    
    $suffixes = array('');
    
    if ('\\' === \DIRECTORY_SEPARATOR) {
      $pathExt = getenv('PATHEXT');
      
      $suffixes = array_merge($pathExt ? explode(PATH_SEPARATOR, $pathExt) : $this->suffixes, $suffixes);
    }
    
    foreach ($suffixes as $suffix) {
      foreach ($dirs as $dir) {
        if (@is_file($file = $dir . \DIRECTORY_SEPARATOR . $name . $suffix) && ('\\' === \DIRECTORY_SEPARATOR || @is_executable($file))) {
          return $file;
        }
      }
    }
    
    return $default;
  }
}