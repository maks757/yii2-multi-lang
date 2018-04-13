<?php
/**
 * Created by PhpStorm.
 * User: cherednyk
 * Date: 12.04.18
 * Time: 17:59
 */

namespace maks757\multilang\components;


use yii\base\InvalidConfigException;

interface ILanguage
{
    /**
     * Get current language symbol
     *
     * @return string
     */
    public function getLanguageChar();

    /**
     * Search language from symbol
     *
     * @param string $lang_id
     * @return ILanguage
     */
    public static function getLanguage($lang_id);

    /**
     * Get current ILanguage
     *
     * @return ILanguage
     */
    public static function getCurrent();

    /**
     * Get deffault ILanguage
     *
     * @return ILanguage
     */
    public static function getDefault();
}