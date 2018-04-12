<?php

namespace maks757\multilang;

use maks757\multilang\entities\Language;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\UrlManager as BaseUrlManager;
use yii\web\UrlManager;

class MultiLangUrlManagerV2 extends UrlManager {

    public $_language_class = 'maks757\multilang\entities\';
    private $_language = null;
    
    public function init()
    {
        
        return parent::init();
    }

    public function createUrl($params)
    {
        $params[0] = 'ru' . '/' . $params[0];
        return parent::createUrl($params);
    }

    public function parseRequest($request)
    {
        $path = ltrim($request->getPathInfo(), 'ru/');
        $request->setPathInfo($path);
        return parent::parseRequest($request);
    }
}