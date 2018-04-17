Multi-language Extension for Yii 2
=====================================

INSTALLATION
------------

### You can use this migrate language table or yours ActiveRecord model.
```text
yii migrate --migrationPath=@vendor/maks757/yii2-multi-lang/migration
```

### If you use yours ActiveRecord model, please implements from ILanguage interface. 
```text
maks757\multilang\components\ILanguage
```

### main.php configure UrlManager
```text
'UrlManager' => [
	'class' => 'maks757\multilang\MultiLangUrlManager'
	...
]
```

### add to ActiveRecord configure TranslationBehavior
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
