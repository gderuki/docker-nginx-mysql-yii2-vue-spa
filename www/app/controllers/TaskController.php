<?php

namespace app\controllers;

use app\models\Task;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;
use \yii\web\NotFoundHttpException;

/**
 * A CRUD actions and simple RESTful API for the Task model.
 */
class TaskController extends ActiveController
{
    public $modelClass = 'app\models\Task';

    /**
     * Returns an array of actions that this controller should handle.
     *
     * @return array the actions that this controller should handle
     */
    public function actions(): array
    {
        $actions = parent::actions();

        // custom process below
        unset(
            $actions['create'],
            $actions['update'],
            $actions['delete'],
            $actions['index']  // Unset the default 'index' action
        );

        return $actions;
    }

    public function actionIndex()
    {
        $cache = \Yii::$app->cache;

        // Try to get the data from cache
        $tasks = $cache->get('tasks');

        if ($tasks === false) {
            // The data is not in the cache, so retrieve it from the database
            $tasks = $this->modelClass::find()->orderBy(['id' => SORT_DESC])->all();

            // Store the data in the cache for next time
            $cache->set('tasks', $tasks, 3600); // Cache for 1 hour
        }

        return $tasks;
    }

    /**
     * Creates a new Task model based on the received POST data.
     *
     * @return Task the newly created model
     * @throws ServerErrorHttpException if the model cannot be saved
     */
    public function actionCreate()
    {
        $model = new $this->modelClass;
        $req = \Yii::$app->getRequest()->getBodyParams();
        $model->load($req, '');

        if ($model->save()) {
            return $model;
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    /**
     * Updates the Task model based on its primary key value and the received POST data.
     *
     * @param integer $id
     * @return Task the updated model or the model without changes if the update is unsuccessful
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(\Yii::$app->request->post(), '') && $model->save()) {
            // Invalidate the cache
            \Yii::$app->cache->delete('tasks');

            return $model;
        }

        throw new BadRequestHttpException('Update failed');
    }

    /**
     * Deletes the Task model based on its primary key value.
     *
     * @param integer $id
     * @return array with a status key indicating success
     * @throws BadRequestHttpException if the model cannot be found or deletion is unsuccessful
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete()) {
            // Invalidate the cache
            \Yii::$app->cache->delete('tasks');

            return ['status' => 'success'];
        }

        throw new BadRequestHttpException('Delete failed');
    }

    /**
     * Finds the Task model based on its primary key value.
     *
     * @param integer $id
     * @return Task the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $cache = \Yii::$app->cache;
        $model = $cache->get('task_' . $id);

        if ($model === false) {
            $model = Task::findOne($id);

            if ($model !== null) {
                $cache->set('task_' . $id, $model, 3600); // 1h
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        return $model;
    }
}
