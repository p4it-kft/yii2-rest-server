<?php
namespace p4it\rest\server\data;


use p4it\rest\server\resources\ValueResource;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

class TransformActiveDataProvider extends ActiveDataProvider {
    /**
     * @var callable
     */
    public $transform;
    
    public function getModels()
    {
        $models = parent::getModels();

        foreach ($models as $key => $model) {
            $returnModels[$key] = call_user_func($this->transform, $model);
        }
        
        return $returnModels; // TODO: Change the autogenerated stub
    }

}