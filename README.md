Multi-language Extension for Yii 2
=====================================

INSTALLATION
------------

### You can use this migrate language table or yours ActiveRecord model.

	yii migrate --migrationPath=@vendor/black-lamp/yii2-multi-lang/migration
	
### If you use yours ActiveRecord model, please implements from ILanguage interface. 

```text
maks757\multilang\components\ILanguage
```

### Configure UrlManager

	'UrlManager' => [
		'class' => 'maks757\multilang\MultiLangUrlManager'
		...
	]

### Configure TranslationBehavior

```php
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => ArticleTranslation::className(),
                'relationColumn' => 'article_id'
            ]
        ];
    }
```
