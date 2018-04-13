<?php

namespace maks757\multilang;

use maks757\multilang\components\ILanguage;
use maks757\multilang\entities\Language;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\UnknownClassException;
use yii\di\NotInstantiableException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\UrlManager as BaseUrlManager;
use yii\web\UrlManager;

/**
 * @property ILanguage $_language;
*/

class MultiLangUrlManager extends UrlManager {

    public $_language_class = 'maks757\multilang\entities\Language';
    private $_language = null;
    
    public function init()
    {
        if(!empty($this->_language_class)) {
            $this->_language = Yii::createObject($this->_language_class);
            if (!$this->_language instanceof ILanguage) {
                throw new NotInstantiableException();
            }
        } else {
            throw new UnknownClassException();
        }
        return parent::init();
    }

    public function createUrl($params)
    {
        $_use_language = '';
        $_path_language = $this->getUrlLanguage($params[0]);
        $_app_language = Yii::$app->language;
        if(!empty($_path_language)) {
            $_use_language = $_path_language;
        } else {
            $_use_language = $_app_language;
        }
        Yii::$app->language = $_use_language;
        $params[0] = $_use_language . '/' . $params[0];
        return parent::createUrl($params);
    }

    public function parseRequest($request)
    {
        $_path_language = $this->getUrlLanguage($request->getPathInfo());
        if(!empty($_path_language)) {
            Yii::$app->language = $_path_language;
        }
        $path = ltrim($request->getPathInfo(), $_path_language.'/');
        $request->setPathInfo($path);
        return parent::parseRequest($request);
    }

    private function getUrlLanguage($url)
    {
        $array_path = explode('/', $url);
        $language = $this->_language::getLanguage(isset($array_path[0]) ? $array_path[0] : null);
        return !empty($language) ? $language->getLanguageChar() : null;
    }
}