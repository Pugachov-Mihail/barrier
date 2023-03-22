<?php

use yii\db\Migration;

/**
 * Class m230306_133005_history_send_info
 */
class m230306_133005_history_send_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            "{{%history_send_info}}",
            [
                'id' => $this->primaryKey(),
                'data_response' => $this->json()->null()->defaultValue(null),
                'date_send' => $this->integer(11)->null()->defaultValue(null),
                'response_status' => $this->boolean()->null()->defaultValue(null),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{%history_send_info}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230306_133005_history_send_info cannot be reverted.\n";

        return false;
    }
    */
}