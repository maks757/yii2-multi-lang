<?php
namespace bl\multilang\behaviors;

use bl\multilang\entities\Language;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecordInterface;

/**
 * Class SeoDataBehavior
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 * @package bl\multilang\behaviors
 *
 * ```php
 *  public function behaviors() {
 *      return [
 *              'translation' => [
 *              'class' => TranslationBehavior::className(),
 *              'translationClass' => ArticleTranslation::className(),
 *              'relationColumn' => 'article_id'
 *          ]
 *      ];
 *  }
 * ```
 * @property ActiveRecordInterface $owner
 * @property string $translationClass
 * @property string $relationColumn
 */
class TranslationBehavior extends Behavior
{
    public $translationClass;
    public $relationColumn;

    public function getTranslation($languageId = null) {
        /* @var $modelClass ActiveRecordInterface */
        /* @var $language ActiveRecordInterface */
        /* @var $translation ActiveRecordInterface */

        $modelClass = $this->translationClass;

        if(!empty($languageId)) {
            $language = Language::findOne($languageId);
            if($language) {
                $translation = $modelClass::findOne([
                    'language_id' => $language->getPrimaryKey(),
                    $this->relationColumn => $this->owner->getPrimaryKey()
                ]);

                if($translation) {
                    return $translation;
                }
            }
        }

        $language = Language::findOne(['lang_id' => Yii::$app->language]);

        // try to find translation on current language
        $translation =  $modelClass::findOne([
            'language_id' => $language->getPrimaryKey(),
            $this->relationColumn => $this->owner->getPrimaryKey()
        ]);

        if(!$translation) {
            // get default language
            $language = Language::findOne(['default' => true]);

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