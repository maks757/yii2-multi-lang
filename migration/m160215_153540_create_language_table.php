<?php

use yii\db\Schema;
use yii\db\Migration;

class m160215_153540_create_language_table extends Migration {

    public function up() {
        $this->createTable('language', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'lang_id' => $this->string(10)->notNull(),
            'show' => 'tinyint(1) UNSIGNED NOT NULL DEFAULT 0',
            'active' => 'tinyint(1) UNSIGNED NOT NULL DEFAULT 0',
        ]);
        $this->createIndex('language_lang_id_index', 'language', ['lang_id']);
    }

    public function down() {
        $this->dropTable('language');
        return true;
    }

    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
