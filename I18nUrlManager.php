<?php

namespace bl\multilang;

use bl\multilang\entities\Language;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\UrlManager as BaseUrlManager;
use const YII_ENV_TEST;

class MultiLangUrlManager extends BaseUrlManager {

    public $languages = [];

    public $enableLocaleUrls = true;
    public $enableDefaultLanguageUrlCode = false;
    /**
     * @var bool whether to detect the app language from the HTTP headers (i.e. browser settings).
     */
    public $enableLanguageDetection = true;
    public $enableLanguagePersistence = true;
    public $keepUppercaseLanguageCode = false;
    /**
     * @var string the name of the session key that is used to store the language.
     */
    public $languageSessionKey = '_language';
    /**
     * @var string the name of the language cookie.
     */
    public $languageCookieName = '_language';
    /**
     * @var int number of seconds how long the language information should be stored in cookie,
     * if `$enableLanguagePersistence` is true. Set to `false` to disable the language cookie completely.
     * Default is 30 days.
     */
    public $languageCookieDuration = 2592000;
    public $languageCookieOptions = [];

    protected $_defaultLanguage;
    /**
     * @inheritdoc
     */
    public $enablePrettyUrl = true;
    public $languageParam = 'language';
    /**
     * @var Request
     */
    protected $_request;
    /**
     * @var bool whether locale URL was processed
     */
    protected $_processed = false;
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->languages = Language::find()->asArray()->select('lang_id')->where(['active' => true])->all();
        $this->languages = ArrayHelper::getColumn($this->languages, 'lang_id');
        if ($this->languages) {
            if (!$this->enablePrettyUrl) {
                throw new InvalidConfigException('Locale URL support requires enablePrettyUrl to be set to true.');
            }
        }
        $this->_defaultLanguage = Yii::$app->language;
        parent::init();
    }
    /**
     * @return string the `language` option that was initially set in the application config file,
     * before it was modified by this component.
     */
    public function getDefaultLanguage()
    {
        return $this->_defaultLanguage;
    }
    /**
     * @inheritdoc
     */
    public function parseRequest($request)
    {
        if ($this->enableLocaleUrls && $this->languages) {
            $process = true;
            if ($process && !$this->_processed) {
                $this->_processed = true;
                $this->processLocaleUrl($request);
            }
        }
        return parent::parseRequest($request);
    }
    /**
     * @inheritdoc
     */
    public function createUrl($params)
    {
        if ($this->enableLocaleUrls && $this->languages) {
            $params = (array) $params;
            if (isset($params[$this->languageParam])) {
                $language = $params[$this->languageParam];
                unset($params[$this->languageParam]);
                $languageRequired = true;
            } else {
                $language = Yii::$app->language;
                $languageRequired = false;
            }
            if (
                $languageRequired && $language===$this->getDefaultLanguage() &&
                !$this->enableDefaultLanguageUrlCode && !$this->enableLanguagePersistence && !$this->enableLanguageDetection
            ) {
                $languageRequired = false;
            }
            $url = parent::createUrl($params);
            if (!$languageRequired && !$this->enableDefaultLanguageUrlCode && $language===$this->getDefaultLanguage()) {
                return  $url;
            } else {
                $key = array_search($language, $this->languages);
                $base = $this->showScriptName ? $this->getScriptUrl() : $this->getBaseUrl();
                $length = strlen($base);
                if (is_string($key)) {
                    $language = $key;
                }
                if (!$this->keepUppercaseLanguageCode) {
                    $language = strtolower($language);
                }
                if ($this->suffix!=='/') {
                    if (count($params)!==1) {
                        $url = preg_replace('#/\?#', '?', $url);
                    } else {
                        $url = rtrim($url, '/');
                    }
                }
                return $length ? substr_replace($url, "$base/$language", 0, $length) : "/$language$url";
            }
        } else {
            return parent::createUrl($params);
        }
    }
    /**
     * Checks for a language or locale parameter in the URL and rewrites the pathInfo if found.
     * If no parameter is found it will try to detect the language from persistent storage (session /
     * cookie) or from browser settings.
     *
     * @var Request $request
     */
    protected function processLocaleUrl($request)
    {
        $this->_request = $request;
        $pathInfo = $request->getPathInfo();
        $parts = [];
        foreach ($this->languages as $k => $v) {
            $value = is_string($k) ? $k : $v;
            if (substr($value, -2)==='-*') {
                $lng = substr($value, 0, -2);
                $parts[] = "$lng\-[a-z]{2,3}";
                $parts[] = $lng;
            } else {
                $parts[] = $value;
            }
        }
        $pattern = implode('|', $parts);
        if (preg_match("#^($pattern)\b(/?)#i", $pathInfo, $m)) {
            $request->setPathInfo(mb_substr($pathInfo, mb_strlen($m[1].$m[2])));
            $code = $m[1];
            if (isset($this->languages[$code])) {
                
                $language = $this->languages[$code];
            } else {
                
                list($language,$country) = $this->matchCode($code);
                if ($country!==null) {
                    if ($code==="$language-$country" && !$this->keepUppercaseLanguageCode) {
                        $this->redirectToLanguage(strtolower($code));   
                    } else {
                        $language = "$language-$country";
                    }
                }
                if ($language===null) {
                    $language = $code;
                }
            }
            Yii::$app->language = $language;
            if ($this->enableLanguagePersistence) {
                Yii::$app->session[$this->languageSessionKey] = $language;
                if ($this->languageCookieDuration) {
                    $cookie = new Cookie(array_merge(
                        ['httpOnly' => true],
                        $this->languageCookieOptions,
                        [
                            'name' => $this->languageCookieName,
                            'value' => $language,
                            'expire' => time() + (int) $this->languageCookieDuration,
                        ]
                    ));
                    Yii::$app->getResponse()->getCookies()->add($cookie);
                }
            }
            
            
            if (!$this->enableDefaultLanguageUrlCode && $language===$this->_defaultLanguage) {
                $this->redirectToLanguage('');
            }
        } else {
            $language = null;
            if ($this->enableLanguagePersistence) {
                $language = Yii::$app->session->get($this->languageSessionKey);
                if ($language===null) {
                    $language = $request->getCookies()->getValue($this->languageCookieName);
                }
            }
            if ($language===null && $this->enableLanguageDetection) {
                foreach ($request->getAcceptableLanguages() as $acceptable) {
                    list($language,$country) = $this->matchCode($acceptable);
                    if ($language!==null) {
                        $language = $country===null ? $language : "$language-$country";
                        break;
                    }
                }
            }
            if ($language===null || $language===$this->_defaultLanguage) {
                if (!$this->enableDefaultLanguageUrlCode) {
                    return;
                } else {
                    $language = $this->_defaultLanguage;
                }
            }
            
            if ($this->matchCode($language)===[null, null]) {
                return;
            }
            $key = array_search($language, $this->languages);
            if ($key && is_string($key)) {
                $language = $key;
            }
            $this->redirectToLanguage($this->keepUppercaseLanguageCode ? $language : strtolower($language));
        }
    }
    
    protected function matchCode($code)
    {
        $language = $code;
        $country = null;
        $parts = explode('-', $code);
        if (count($parts)===2) {
            $language = $parts[0];
            $country = strtoupper($parts[1]);
        }
        if (in_array($code, $this->languages)) {
            return [$language, $country];
        } elseif (
            $country && in_array("$language-$country", $this->languages) ||
            in_array("$language-*", $this->languages)
        ) {
            return [$language, $country];
        } elseif (in_array($language, $this->languages)) {
            return [$language, null];
        } else {
            return [null, null];
        }
    }
    /**
     * Redirect to the current URL with given language code applied
     * @param string $language the language code to add. Can also be empty to not add any language code.
     */
    protected function redirectToLanguage($language)
    {
        $result = parent::parseRequest($this->_request);
        if ($result === false) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        list ($route, $params) = $result;
        if($language){
            $params[$this->languageParam] = $language;
        }
        
        $params = $params + $this->_request->getQueryParams();
        array_unshift($params, $route);
        $url = $this->createUrl($params);
        
        if ($this->suffix==='/' && $route==='') {
            $url = rtrim($url, '/').'/';
        }
        Yii::$app->getResponse()->redirect($url);
        if (YII_ENV_TEST) {
            
            
            throw new Exception(Url::to($url));
        } else {
            Yii::$app->end();
        }
    }
}