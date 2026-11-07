<?php

use yii\db\Migration;

/**
 * Initial schema from production dump j649859_dd (structure only).
 */
class m261107_194800_init_schema extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci';

        $this->createTable('{{%categories}}', [
            'id' => $this->primaryKey(),
            'parent' => $this->integer()->null(),
            'name' => $this->string(250)->notNull(),
            'description' => $this->string(255)->notNull(),
            'source' => $this->smallInteger(1)->notNull(),
            'date' => $this->integer()->notNull(),
            'show_default' => $this->boolean()->defaultValue(1),
        ], $tableOptions);

        $this->createTable('{{%costs}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(250)->notNull(),
            'category' => $this->integer()->notNull(),
            'category_child' => $this->integer()->notNull(),
            'costs_default' => $this->integer()->null(),
            'cost' => $this->integer()->notNull(),
            'score' => $this->integer()->notNull(),
            'check_for_days' => $this->boolean()->null(),
            'date' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%costs_default}}', [
            'id' => $this->primaryKey(),
            'category' => $this->integer()->notNull(),
            'name' => $this->string(250)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%incomes}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(250)->notNull(),
            'income_default' => $this->integer()->notNull(),
            'income' => $this->integer()->notNull(),
            'date' => $this->integer()->notNull(),
            'category' => $this->integer()->null(),
            'score' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%incomes_default}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(250)->notNull(),
            'category' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%invest}}', [
            'id' => $this->primaryKey(),
            'invest_id' => $this->integer()->notNull(),
            'summ' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%logs}}', [
            'id' => $this->primaryKey(),
            'type_id' => $this->integer()->notNull(),
            'value' => $this->integer()->notNull(),
            'name' => $this->string(255)->null(),
            'desc' => $this->string(255)->null(),
            'score_id' => $this->integer()->null(),
            'payment_id' => $this->integer()->null(),
            'created_at' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
        ], $tableOptions);

        $this->createTable('{{%payments}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(250)->null(),
            'summa' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%save}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->null(),
            'summa' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%scores}}', [
            'id' => $this->primaryKey(),
            'is_check' => $this->boolean()->notNull(),
            'name' => $this->string(250)->notNull(),
            'description' => $this->text()->notNull(),
            'id_default' => $this->boolean()->notNull(),
            'summa' => $this->integer()->notNull(),
            'credit_limit' => $this->integer()->null(),
        ], $tableOptions);

        $this->createTable('{{%settings}}', [
            'id' => $this->primaryKey(),
            'time_begin' => $this->integer()->notNull(),
            'time_end' => $this->integer()->notNull(),
            'month_limit' => $this->integer()->null(),
            'notes_1' => $this->text()->null(),
            'notes_2' => $this->text()->null(),
            'notes_3' => $this->text()->null(),
            'notes_4' => $this->text()->null(),
        ], $tableOptions);

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey()->unsigned(),
            'username' => $this->string(255)->notNull(),
            'password' => $this->string(255)->notNull(),
            'role' => $this->string(255)->notNull()->defaultValue('user'),
        ], $tableOptions);

        $this->createIndex('email', '{{%user}}', 'username', true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
        $this->dropTable('{{%settings}}');
        $this->dropTable('{{%scores}}');
        $this->dropTable('{{%save}}');
        $this->dropTable('{{%payments}}');
        $this->dropTable('{{%logs}}');
        $this->dropTable('{{%invest}}');
        $this->dropTable('{{%incomes_default}}');
        $this->dropTable('{{%incomes}}');
        $this->dropTable('{{%costs_default}}');
        $this->dropTable('{{%costs}}');
        $this->dropTable('{{%categories}}');
    }
}
