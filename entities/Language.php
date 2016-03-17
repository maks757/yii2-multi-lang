<?php

namespace bl\multilang\entities;

use yii\db\ActiveRecord;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class Language extends ActiveRecord {

    public static function tableName() {
        return 'language';
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
