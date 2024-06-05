<?php

use yii\db\Migration;

/**
 * Class m240605_115135_seed_book_table
 */
class m240605_115135_seed_book_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insertFakeBooks();
    }

    /**
     * {@inheritdoc}
     */
    private function insertFakeBooks()
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 50; $i++) {
            $this->insert(
                'book',
                [
                    'title' => $faker->sentence(),
                    'author' => $faker->name,
                    'iban' => $faker->iban(),
                    'release_year' => (int) $faker->year,
                    'cover_image' => $faker->imageUrl()
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
        echo "m240605_115135_seed_book_table cannot be reverted.\n";

        return false;
    }
    */
}
