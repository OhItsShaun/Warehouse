<?php

class Code {

    static function getRegexs() {
        return array(
            '/^`{3}(.+?)\n((.+\n)+?)`{3}$/m' => "blockWithLanguage",   // Code block with language specified
            '/^`{3}\n((.+\n)+?)`{3}$/m' => "blockWithoutLanguage",   // Code block without language specified
            '/`(.+?)`/' => "inlineCodeHandler",           // `code`
        );
    }

	/**
	 * Formats inline-code wrapped by `code`
	 * @param  string $matches The code to wrap
	 * @return string   Wrapped code
	 */
	public static function inlineCodeHandler ($matches) {
        return "<code>" . self::codeStrip($matches[1]) . "</code>";
	}

	/**
	 * Handler for a block of code
	 * @param  array $matches [1] The language name, [2] The code block
	 * @return string   The formatted text
	 */
	public static function blockWithLanguage ($matches) {
		return "<pre data-type=\"code\" data-language=\"". $matches[1] . "\"><code class=\"" . strtolower($matches[1]) . "\">" . self::codeStrip($matches[2]) . "</code></pre>\n";
	}

   public static function blockWithoutLanguage ($matches) {
       return "<pre data-type=\"code\" data-language=\"code\"><code>" . self::codeStrip($matches[1]) . "</code></pre>\n";
   }

    /**
     * HTML code inside <code> tags still renders as HTML. We need to replaec the < and > tags with their character codes to prevent this.
     * @param  string $text Code to strip of it's < and > symbols
     * @return string Code with safe < >
     */
    private static function codeStrip ($text) {
        $text = preg_replace("/</", "&lt;", $text);
        $text = preg_replace("/>/", "&gt;", $text);
        return $text;
    }

}

?>
