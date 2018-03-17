<?php

use yii\db\Migration;

class m180317_200346_init_commit extends Migration
{

    public function safeUp()
    {
        $this->createTable('mailing_list_membership', [
            'id' => $this->primaryKey(),
            'email' => $this->string(128)->notNull(),
            'is_member' => $this->boolean(),
            'token' => $this->string(128),
        ]);

        $this->createTable('mailing_list_entry', [
            'id' => $this->primaryKey(),
            'template_instance_id' => $this->integer()->notNull(),
            'is_sent' => $this->boolean()->defaultValue(false)
        ]);

        $this->addForeignKey(
                'fk_mail_template_instance', 'mailing_list_entry', 'template_instance_id', 'template_instance', 'id', 'CASCADE'
        );
    }

    public function safeDown()
    {
        echo "m180317_200346_init_commit cannot be reverted.\n";

        return false;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m180317_200346_init_commit cannot be reverted.\n";

      return false;
      }
     */
}
