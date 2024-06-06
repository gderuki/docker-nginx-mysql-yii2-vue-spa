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
            $actions['delete']
        );

        $actions['index'] = [
            'class' => 'yii\rest\IndexAction',
            'modelClass' => $this->modelClass,
            'prepareDataProvider' => fn() => new ActiveDataProvider(
                [
                    'query'=> $this->modelClass::find()->orderBy(['id' => SORT_DESC]),
                    'pagination' => false,
                ]
            ),
        ];

        return $actions;
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
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}