<?php

use yii\db\Migration;

/**
 * Class m240605_115135_seed_task_table
 */
class m240605_115135_seed_task_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insertFakeTasks();
    }

    /**
     * {@inheritdoc}
     */
    private function insertFakeTasks()
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 25; $i++) {
            $this->insert(
                'task',
                [
                    'title' => $faker->sentence(),
                    'description' => $faker->paragraph(),
                    'status' => $faker->numberBetween(0, 1),
                ]
            );
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240605_115135_seed_task_table cannot be reverted.\n";

        return false;
    }
    */
}
