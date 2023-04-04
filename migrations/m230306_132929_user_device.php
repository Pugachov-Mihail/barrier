<?php

use yii\db\Migration;

/**
 * Class m230306_132929_user_device
 */
class m230306_132929_user_device extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            '{{%user_device}}',
            [
                'id' => $this->primaryKey(),
                'id_inom' => $this->integer(11)->null()->defaultValue(null),
                'name' => $this->string(11)->null()->defaultValue(null),
                'password' => $this->string(50)->null()->defaultValue(null),
                'password_reset' => $this->string(50)->null()->defaultValue(null),
                'create_at' => $this->integer(11)->null()->defaultValue(null),
                'update_at' => $this->integer(11)->null()->defaultValue(null),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_device}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230306_132929_user_device cannot be reverted.\n";

        return false;
    }
    */
}
