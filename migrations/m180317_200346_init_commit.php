<?php

use yii\db\Migration;

class m180317_200346_init_commit extends Migration
{

    protected $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

    public function safeUp()
    {
        $this->createTable('mailing_list_subscriber', [
            'id' => $this->primaryKey(),
            'email' => $this->string(128)->notNull()->unique(),
            'is_member' => $this->boolean(),
            'token' => $this->string(128),
                ], $this->tableOptions);

        $this->createTable('mailing_list_entry', [
            'id' => $this->primaryKey(),
            'page_id' => $this->integer(),
            'container_page_id' => $this->integer(),
            'sent_at' => $this->dateTime()->null(),

        ], $this->tableOptions);

        $this->addForeignKey(
            'fk_mail_template_instance', 'mailing_list_entry', 'page_id',
            'custom_pages_page', 'id', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_mail_template_instance', 'mailing_list_entry', 'container_page_id',
            'custom_pages_container_page', 'id', 'CASCADE'
        );
    }

    public function safeDown()
    {
        echo "m180317_200346_init_commit cannot be reverted.\n";

        return false;
    }
}
