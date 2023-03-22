<?php

use yii\db\Migration;

/**
 * Class m230306_132818_debtor
 */
class m230306_132818_debtor extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            '{{%debtor}}',
            [
                'id' => $this->primaryKey(),
                'inom_id' => $this->integer(11)->null()->defaultValue(null),
                'debt' => $this->integer(11)->null()->defaultValue(null),
                'date_start_debt' => $this->integer(11)->null()->defaultValue(null),
                'credit' => $this->integer(11)->null()->defaultValue(null),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%debtor}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230306_132818_debtor cannot be reverted.\n";

        return false;
    }
    */
}
