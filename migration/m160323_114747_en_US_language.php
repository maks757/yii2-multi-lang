<?php

use yii\db\Migration;

class m160323_114747_en_US_language extends Migration
{
    public function safeUp()
    {
        $this->insert('language', [
            'name' => 'English',
            'lang_id' => 'en-US',
            'show' => true,
            'active' => true,
            'default' => true
        ]);

        return true;
    }

    public function safeDown()
    {
        $this->delete('language', [
            'lang_id' => 'en-US'
        ]);

        return true;
    }
}
