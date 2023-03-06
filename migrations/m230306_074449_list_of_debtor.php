<?php

use yii\db\Migration;

/**
 * Class m230306_074449_list_of_debtor
 */
class m230306_074449_list_of_debtor extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            "{{%list_of_debtor}}",
            [
                'id' => $this->primaryKey(11),
                'inom_id' => $this->integer(11)->null()->defaultValue(null),
                'lastname' => $this->string(255)->null()->defaultValue(null),
                'firstname' => $this->string(255)->null()->defaultValue(null),
                'middlename' => $this->string(255)->null()->defaultValue(null),
                'phone' => $this->string(50)->null()->defaultValue(null),
                'type_user' => $this->integer(11)->null()->defaultValue(null),
                'status' => $this->integer(11)->null()->defaultValue(null),
                'type_sync' => $this->integer(11)->null()->defaultValue(null),
                'self_id' => $this->integer(11)->null()->defaultValue(null),
                'open_gate' => $this->integer(11)->null()->defaultValue(null),
                'created_at' => $this->integer(11)->null()->defaultValue(null),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{%list_of_debtor}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230306_074449_list_of_debtor cannot be reverted.\n";

        return false;
    }
    */
}
