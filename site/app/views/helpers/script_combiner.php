<?php 
/**
 * The helper definition for the Script Combiner helper.
 * @author Geoffrey Garbers
 * @version 0.1
 */

// Include the stand-alone configuration file if it exists.
if (is_file(CONFIGS . 'script_combiner.php')) {
    Configure::load('script_combiner');
}

/**
 * @property HtmlHelper $Html
 * @property JavascriptHelper $Javascript
 */
class ScriptCombinerHelper extends Helper {
/**
 * Array containing additional helpers to be used in this helper.
 * @access public
 * @var array
 */
    public $helpers = array('Html', 'Javascript');
/**
 * The directory to which the combined CSS files will be cached.
 * @access private
 * @var string
 */
    private $cssCachePath;
/**
 * The directory to which the combined Javascript files will be cached.
 * @access private
 * @var string
 */
    private $jsCachePath;
/**
 * Indicates the time of expiry for the combined files.
 * @access private
 * @var int
 */
    private $cacheLength;
/**
 * Indicates whether the class is active and is able to begin combining scripts.
 * @access private
 * @var bool
 */
    private $enabled = false;
/**
 * Sets up the helper's properties, and determines whether the helper's environment
 * is set up and ready to be used.
 * @access public
 * @return void
 */
    public function __construct() {
        parent::__construct();

        // Check to make there are configuration options that can be found.
        if (!Configure::read('ScriptCombiner')) {
            trigger_error('Please define the ScriptCombiner configuration options.', E_USER_WARNING);
            return;
        }

        // Retrieve the CSS cache path, and ensure the path exists and is writable.
        $this->cssCachePath = Configure::read('ScriptCombiner.cssCachePath');
        if (!is_dir($this->cssCachePath)) {
            trigger_error('Cannot locate CSS combination cache directory at ' . $this->cssCachePath . ', or path is not writable.', E_USER_WARNING);
            return;
        }

        // Retrieve the Javascript cache path, and check to ensure that the path
        // is existing and writable.
        $this->jsCachePath = Configure::read('ScriptCombiner.jsCachePath');
        if (!is_dir($this->jsCachePath) || !is_writable($this->jsCachePath)) {
            trigger_error('Cannot locate Javascript combination cache directory at ' . $this->jsCachePath . ', or path is not writable.', E_USER_WARNING);
            return;
        }

        $cacheLength = Configure::read('ScriptCombiner.cacheLength');
        if (is_string($cacheLength)) {
            $this->cacheLength = strtotime($cacheLength) - time();
        } else {
            $this->cacheLength = (int)$cacheLength;
        }
        
        $this->enabled = true;
    }
/**
 * <p>Receives numerous CSS files, and combines all the supplied CSS files into one
 * file, which helps in reducing the number of HTTP requests needed to load numerous
 * CSS files.</p>
 *
 * <p>Files to be combined should be supplied exactly as if they were being used in
 * the HtmlHelper::css() method, as this method is used to generate the paths
 * to the files.</p>
 * @access public
 * @param mixed [$url1,$url2,...] Either an array of files to combine, or multiple arguments of filenames.
 * @return string The HTML &lt;link /&gt; to either the combined file, or to the multiple CSS files if the combined file could not be cached.
 */
    public function css() {
        // Get the CSS files.
        $cssFiles = func_get_args();

        // Oh dear. There aren't any files to process. We'll have to return an empty
        // string.
        if (empty($cssFiles)) {
            return '';
        }

        // Whoops. No configuration options defined, or something else went wrong
        // in trying to set up the class. Either way, we can't process the files,
        // so we'll need to handle this through the parent.
        if (!$this->enabled) {
            return $this->Html->css($cssFiles);
        }

        // Let's generate the cache hash, and ensure we have all the files that
        // we are going to use to process.
        if (is_array($cssFiles[0])) {
            $cssFiles = $cssFiles[0];
        }
        $cacheKey = md5(serialize($cssFiles));

        // Let's generate the path to the cache file.
        $cacheFile = "{$this->cssCachePath}combined.{$cacheKey}.css";

        // Oh. Look. It appears we already have a cached version of the combined
        // file. This means we'll have to see when it last modified, and ensure
        // that the cached version hasn't yet expired. If it has, then we can
        // just return the URL to it straight away.
        if ($this->isCacheFileValid($cacheFile)) {
            return $this->Html->css($this->convertToUrl($cacheFile));
        }

        // Let's generate the HTML that would normally be returned, and strip
        // out the URLs.
        $cssData = array();
        $links = $this->Html->css($cssFiles, 'import');
        preg_match_all('#\(([^\)]+)\)#i', $links, $urlMatches);
        if (isset($urlMatches[1])) {
            $urlMatches = $urlMatches[1];
        } else {
            $urlMatches = array();
        }

        // Let's cycle through each URL, and attempt to retrieve the file contents.
        // If we *can* get the file data successfully, then we'll add it to the
        // array of data to combine.
        foreach ($urlMatches as $urlMatch) {
            $cssPath = str_replace(array('/', '\\'), DS, WWW_ROOT . ltrim(Router::normalize($urlMatch), '/'));
            if (is_file($cssPath)) {
                $cssData[] = file_get_contents($cssPath);
            }
        }

        // Let's combine them.
        $cssData = implode(Configure::read('ScriptCombiner.fileSeparator'), $cssData);

        // Let's check whether we need to compress the CSS. If so, we'll compress
        // it before saving it.
        if (Configure::read('ScriptCombiner.compressCss')) {
            $cssData = $this->compressCss($cssData);
        }

        // If we can cache the file, then we can return the URL to the file.
        if (file_put_contents($cacheFile, $cssData) > 0) {
            return $this->Html->css($this->convertToUrl($cacheFile));
        }

        // Otherwise, we'll have to trigger an error, and pass the handling of the
        // CSS files to the HTML Helper.
        trigger_error("Cannot combine CSS files to {$cacheFile}. Please ensure this directory is writable.", E_USER_WARNING);
        return $this->Html->css($cssFiles);
    }
/**
 * Receives a number of Javascript files, and combines all of them together.
 * @access public
 * @param mixed [$url1,$url2,...] Either an array of files to combine, or multiple arguments of filenames.
 * @return string The HTML &lt;script /&gt; to either the combined file, or to the multiple Javascript files if the combined file could not be cached.
 */
    public function js() {
        // Get the javascript files.
        $jsFiles = func_get_args();

        // Whoops. No files! We'll have to return an empty string then.
        if (empty($jsFiles)) {
            return '';
        }

        // If the helper hasn't been set up correctly, then there's no point in
        // combining scripts. We'll pass it off to the parent to handle.
        if (!$this->enabled) {
            return $this->Javascript->link($jsFiles);
        }

        // Let's make sure we have the array of files correct. And we'll generate
        // a key for the cache based on the files supplied.
        if (is_array($jsFiles[0])) {
            $jsFiles = $jsFiles[0];
        }
        $cacheKey = md5(serialize($jsFiles));

        // And we'll generate the absolute path to the cache file.
        $cacheFile = "{$this->jsCachePath}combined.{$cacheKey}.js";

        // If we can determine that the current cache file is still valid, then
        // we can just return the URL to that file.
        if ($this->isCacheFileValid($cacheFile)) {
            return $this->Javascript->link($this->convertToUrl($cacheFile));
        }

        $jsData = array();
        $jsLinks = $this->Javascript->link($jsFiles);
        preg_match_all('/src="([^"]+)"/i', $jsLinks, $urlMatches);
        if (isset($urlMatches[1])) {
            $urlMatches = array_unique($urlMatches[1]);
        } else {
            $urlMatches = array();
        }

        foreach ($urlMatches as $urlMatch) {
            $jsPath = str_replace(array('/', '\\'), DS, WWW_ROOT . ltrim(Router::normalize($urlMatch), '/'));
            if (is_file($jsPath)) {
                $jsData[] = file_get_contents($jsPath);
            }
        }

		

        // Let's combine them.
        $jsData = implode(Configure::read('ScriptCombiner.fileSeparator'), $jsData);

        // Let's check whether we need to compress the Javascript. If so, we'll
        // compress it before saving it.
        if (Configure::read('ScriptCombiner.compressJs')) {
            $jsData = $this->compressJs($jsData);
        }

        // If we can cache the file, then we can return the URL to the file.

        if (file_put_contents($cacheFile, $jsData) > 0) {
            return $this->Javascript->link($this->convertToUrl($cacheFile));
        }

        // Otherwise, we'll have to trigger an error, and pass the handling of the
        // CSS files to the HTML Helper.
        #trigger_error("Cannot combine Javascript files to {$cacheFile}. Please ensure this directory is writable.", E_USER_WARNING);
        return $this->Javascript->link($jsFiles);
    }
/**
 * Indicates whether the supplied cached file's cache life has expired or not.
 * Returns a boolean value indicating this.
 * @access private
 * @param string $cacheFile The path to the cached file to check.
 * @return bool Flag indicating whether the file has expired or not.
 */
    private function isCacheFileValid($cacheFile) {
        if (is_file($cacheFile) && $this->cacheLength > 0) {
            $lastModified = filemtime($cacheFile);
            $timeNow = time();
            if (($timeNow - $lastModified) < $this->cacheLength) {
                return true;
            }
        }

        return false;
    }
/**
 * Receives the path to a given file, and strips the webroot off the file, returning
 * a URL path that is relative to the webroot (WWW_ROOT).
 * @access private
 * @param string $filePath The path to the file.
 * @return string The path to the file, relative to WWW_ROOT (webroot).
 */
    private function convertToUrl($filePath) {
        $___path = Set::filter(explode(DS, $filePath));
        $___root = Set::filter(explode(DS, WWW_ROOT));
        $webroot = array_diff_assoc($___root, $___path);

        $webrootPaths = array_diff_assoc($___path, $___root);
        return ('/' . implode('/', $webrootPaths));
    }
/**
 * Receives the CSS data to compress, and compresses it. Doesn't apply any encoding
 * to it (such as GZIP), but merely strips out unnecessary whitespace.
 * @access private
 * @param string $cssData CSS data to be compressed.
 * @return string Compressed CSS data.
 */
    private function compressCss($cssData) {
        // let's remove all the comments from the css code.
        $cssData = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $cssData);

        // let's remove all tabs and line breaks.
        $cssData = str_replace(array("\r\n", "\r", "\n", "\t"), '', $cssData);

        // remove trailing semicolons just before closing brace.
        $cssData = preg_replace('/;\s*}/i', '}', $cssData);

        // remove any whitespace between element selector and opening brace.
        $cssData = preg_replace('/[\t\s]*{[\t\s]*/i', '{', $cssData);

        // remove whitespace between style declarations and their values.
        $cssData = preg_replace('/[\t\s]*:[\t\s]*/i', ':', $cssData);

        // remove whitespace between sizes and their measurements.
        $cssData = preg_replace('/(\d)[\s\t]+(em|px|%)/i', '$1$2', $cssData);

        // remove any spaces between background image "url" and the opening "(".
        $cssData = preg_replace('/url[\s\t]+\(/i', 'url(', $cssData);

        return $cssData;
    }
/**
 * Compresses the supplied Javascript data, removing extra whitespaces, as well
 * as any comments found.
 * @access private
 * @param string $jsData The Javascript data to be compressed.
 * @return string The compressed Javascript data.
 * @todo Implement reliable Javascript compression without use of a 3rd party.
 */
    private function compressJs($jsData) {
        return $jsData;
    }
}
?>