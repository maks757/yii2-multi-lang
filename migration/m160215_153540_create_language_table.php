<?php

use yii\db\Schema;
use yii\db\Migration;

class m160215_153540_create_language_table extends Migration {

    public function safeUp() {
        $this->createTable('language', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'lang_id' => $this->string(10)->notNull(),
            'show' => $this->boolean(),
            'active' => $this->boolean(),
            'default' => $this->boolean()
        ]);
        $this->createIndex('language_lang_id_index', 'language', ['lang_id']);
    }

    public function safeDown() {
        $this->dropTable('language');
        return true;
    }
}
