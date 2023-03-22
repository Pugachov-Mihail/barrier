<?php

use yii\db\Migration;

/**
 * Class m230306_135733_message_for_debter
 */
class m230306_135733_message_for_debter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            '{{%message_for_debtor}}',
            [
                'id' => $this->primaryKey(),
                'id_inom' => $this->integer(11)->null()->defaultValue(null),
                'phone' => $this->string(50)->null()->defaultValue(null),
                'type_scenary' => $this->integer(11)->null()->defaultValue(null)->comment("Вид сценария"),
                'feedback' => $this->integer(11)->null()->defaultValue(null)->comment("Вид обратной связи"),
                'create_at' => $this->integer(11)->null()->defaultValue(null),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%message_for_debtor}}');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230306_135733_message_for_debter cannot be reverted.\n";

        return false;
    }
    */
}
