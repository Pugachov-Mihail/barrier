<?php

use yii\db\Migration;

/**
 * Class m230306_120352_history_barrier
 */
class m230306_120352_history_barrier extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            "{{%history_barrier}}",
            [
                'id' => $this->primaryKey(),
                'device_id'=> $this->string(150)->null()->defaultValue(null),
                'phone' => $this->string(50)->null()->defaultValue(null),
                'datetime' => $this->integer(11)->null()->defaultValue(null),
                'inom_id' => $this->integer(11)->null()->defaultValue(null),
                'open_gate' => $this->integer(11)->null()->defaultValue(null),
                'message_id' => $this->integer(11)->null()->defaultValue(null),
                'type_user' => $this->integer(11)->null()->defaultValue(null),
                'company_id' => $this->integer(11)->null()->defaultValue(null),
                'company_name' => $this->string(250)->null()->defaultValue(null),
                'send_in_inom' => $this->integer(11)->null()->defaultValue(null),
                'type' => $this->integer(11)->null()->defaultValue(null),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{%history_barrier}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230306_120352_history_barrier cannot be reverted.\n";

        return false;
    }
    */
}
