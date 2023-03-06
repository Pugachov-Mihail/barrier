<?php

use yii\db\Migration;

/**
 * Class m230306_142501_access_token
 */
class m230306_142501_access_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230306_142501_access_token cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230306_142501_access_token cannot be reverted.\n";

        return false;
    }
    */
}
