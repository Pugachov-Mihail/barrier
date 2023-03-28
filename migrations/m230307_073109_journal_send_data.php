<?php

use yii\db\Migration;

/**
 * Class m230307_073109_journal_send_data
 */
class m230307_073109_journal_send_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            "{{%journal_send_data}}",
            [
                'id' => $this->primaryKey(),
                'data_response' => $this->json()->null()->defaultValue(null),
                'date_send' => $this->integer(11)->null()->defaultValue(null),
                'response_status' => $this->boolean()->null()->defaultValue(false),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{%journal_send_data}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230307_073109_journal_send_data cannot be reverted.\n";

        return false;
    }
    */
}
