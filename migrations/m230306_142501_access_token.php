<?php

use yii\db\Migration;

/**
 * Class m230306_142501_access_token
 */
class m230306_142501_access_token extends Migration
{
    private $tableName = '{{%access_token}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $fields = [
            'id' => $this->primaryKey(),
            'device_id' => $this->integer()->notNull(),
            'token' => $this->string(32)->notNull()->unique()->comment("Сгенерированный токен"),
            'created' => $this->integer()->notNull()->comment("Дата создания токена в unix-time"),
            'status'=> $this->integer(11)->null()->defaultValue(null),
            'company_id'=> $this->integer(11)->null()->defaultValue(null)->comment('Компания'),
            'created_at'=> $this->integer(11)->null()->defaultValue(null),
        ];

        $this->createTable($this->tableName, $fields);
        $this->addForeignKey('fk_token_has_device',
        $this->tableName, 'device_id',
        'device', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
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
