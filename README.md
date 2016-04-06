Multi-language Extension for Yii 2
=====================================

INSTALLATION
------------

### Migrate language table

	yii migrate --migrationPath=@vendor/black-lamp/yii2-multi-lang/migration

### Configure UrlManager

	'UrlManager' => [
		'class' => 'bl\multilang\MultiLangUrlManager'
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
