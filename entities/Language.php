<?php

namespace maks757\multilang\entities;

use Yii;
use yii\db\ActiveRecord;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 *
 * @property number id
 * @property string name
 * @property string lang_id
 * @property boolean show
 * @property boolean active
 * @property boolean default
 */
class Language extends ActiveRecord {

    public static function tableName() {
        return 'language';
    }

    /**
     * @return Language
     */
    public static function getDefault() {
        return Language::findOne([
            'default' => true
        ]);
    }

    /**
     * @return Language Current language, or default
     */
    public static function getCurrent() {
        $language = Language::findOne([
            'lang_id' => Yii::$app->language
        ]);

        if(!$language) {
            $language = static::getDefault();
        }

        return $language;
    }

    public static function findOrDefault($languageId) {
        if (empty($languageId) || !$language = Language::findOne($languageId)) {
            $language = Language::find()
                    ->where(['lang_id' => \Yii::$app->sourceLanguage])
                    ->one();
        }
        return $language;
    }

}
