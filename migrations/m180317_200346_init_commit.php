<?php

use yii\db\Migration;

class m180317_200346_init_commit extends Migration
{
    public function safeUp()
    {
        $this->createTable('mailing_list_membership', [
            'id' => $this->primaryKey(),
            'email' => $this->string(128),
            'subscribed' => $this->boolean(),
            'update_token' => $this->string(128),
        ]);

        $this->createTable('mailing_list_entry', [
            'id' => $this->primaryKey(),
            'template_instance_id' => $this->integer(),
            'sent' => $this->boolean()->defaultValue(false)
        ]);

        $this->addForeignKey(
            'fk-mail-template-instance',
            'mailing_list_entry',
            'template_instance_id',
            'template_instance',
            'id',
            'CASCADE'
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
