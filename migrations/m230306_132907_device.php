<?php

use yii\db\Migration;

/**
 * Class m230306_132907_device
 */
class m230306_132907_device extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            '{{%device}}',
            [
                'id' => $this->integer(11)->null()->defaultValue(null),
                'id_device' => $this->integer(11)->null()->defaultValue(null),
                'company_id' => $this->integer(11)->null()->defaultValue(null),
                'ip_sluice' => $this->string(250)->null()->defaultValue(null),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%device}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230306_132907_device cannot be reverted.\n";

        return false;
    }
    */
}
