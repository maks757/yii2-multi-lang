<?php
namespace maks757\multilang\behaviors;

use maks757\multilang\components\ILanguage;
use maks757\multilang\entities\Language;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecordInterface;

/**
 * Class SeoDataBehavior
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 * @author Cherednyk Maxim <maks757q@gmail.com>
 * @package maks757\multilang\behaviors
 *
 * ```php
 *  public function behaviors() {
 *      return [
 *              'translation' => [
 *              'class' => TranslationBehavior::className(),
 *              'language' => 'maks757\multilang\entities\Language',
 *              'translationClass' => ArticleTranslation::className(),
 *              'relationColumn' => 'article_id'
 *          ]
 *      ];
 *  }
 * ```
 * @property ActiveRecordInterface $owner
 * @property ILanguage $language
 * @property string $translationClass
 * @property string $relationColumn
 */
class TranslationBehavior extends Behavior
{
    public $translationClass;
    public $language;
    public $relationColumn;

    public function getTranslation($languageId = null) {
        /* @var $modelClass ActiveRecordInterface */
        /* @var $language ILanguage */
        /* @var $translation ActiveRecordInterface */

        $modelClass = $this->translationClass;
        $language = Yii::createObject($this->language);

        if(!empty($languageId)) {
            $language = $language::findOne($languageId);
            if($language) {
                $translation = $modelClass::findOne([
                    'language_id' => $language->getPrimaryKey(),
                    $this->relationColumn => $this->owner->getPrimaryKey()
                ]);
                return $translation;
            }
        }

        $language = $language::getLanguage(Yii::$app->language);

        // try to find translation on current language
        $translation =  $modelClass::findOne([
            'language_id' => $language->getPrimaryKey(),
            $this->relationColumn => $this->owner->getPrimaryKey()
        ]);

        if(!$translation) {
            // get default language
            $language = $language::getDefault();

            // try to find translation on default language
            $translation =  $modelClass::findOne([
                'language_id' => $language->getPrimaryKey(),
                $this->relationColumn => $this->owner->getPrimaryKey()
            ]);

            if(!$translation) {
                // find any translation
                $translation =  $modelClass::findOne([
                    $this->relationColumn => $this->owner->getPrimaryKey()
                ]);
            }
        }

        return $translation;
    }
}