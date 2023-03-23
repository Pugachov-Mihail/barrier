<?php

use yii\db\Migration;

/**
 * Class m230306_132855_region
 */
class m230306_132855_region extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            '{{%region}}',
            [
                'id' => $this->primaryKey(),
                'list_debtor_id' => $this->integer(11)->null()->defaultValue(null),
                'account_id' => $this->integer(11)->null()->defaultValue(null),
                'company_id' => $this->integer(11)->null()->defaultValue(null),
                'region_id' => $this->integer(11)->null()->defaultValue(null),
                'name_region' => $this->integer(11)->null()->defaultValue(null),
                'inom_id' => $this->integer(11)->null()->defaultValue(null),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%region}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230306_132855_region cannot be reverted.\n";

        return false;
    }
    */
}
