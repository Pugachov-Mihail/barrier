<?php

use yii\db\Migration;

/**
 * Class m230404_082216_user
 */
class m230404_082216_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            'user',
            [
                'id' => $this->primaryKey(),
                'username' => $this->string()->notNull()->unique(),
                'auth_key' => $this->string(50)->null()->defaultValue(null),
                'password' => $this->string()->notNull(),
                'status' => $this->smallInteger()->notNull()->defaultValue(10),
                'created_at' => $this->integer()->null()->defaultValue(null),
                'updated_at' => $this->integer()->null()->defaultValue(null),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("user");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230404_082216_user cannot be reverted.\n";

        return false;
    }
    */
}
