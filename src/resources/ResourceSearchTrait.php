<?php

namespace p4it\rest\server\resources;

use pappco\yii2\components\ActiveQuery;
use pappco\yii2\components\ActiveRecord;
use pappco\yii2\components\ruleSet\RuleSet;
use yii\helpers\ArrayHelper;

/**
 * Trait ResourceSearchTrait
 * @package modules\core\common\components\rest
 *
 * @mixin ActiveRecord
 */
trait ResourceSearchTrait
{

    public $wrapQuery = true;

    public function searchRuleSet()
    {
        $rules = parent::ruleSet()->getFilteredRules('*', ['integer', 'string', 'boolean', 'number', 'each'/*,'date'*/, 'safe']);
        return RuleSet::create($rules)
            ->safe(array_keys($this->attributes));
    }

    public function attributes()
    {
        return ArrayHelper::merge(
            parent::attributes(),
            $this->searchAttributes()
        );
    }

    public function searchAttributes()
    {
        return [];
    }

    public function ruleSet()
    {
        return $this->searchRuleSet();
    }


    /**
     * @return ActiveQuery
     */
    public function searchQuery()
    {
        $query = $this->query();

        if ($this->wrapQuery) {
            $groupBy = $this->query()->groupBy;
            $orderBy = $this->query()->orderBy;
            $query = self::find()->from(['subQuery' => $this->query()->groupBy([])])->select(['*']);
            if($groupBy) {
                $groupBy = array_map(static function ($value){
                    $array = explode('.', $value);
                    return end($array);
                }, $groupBy);

                $query->groupBy($groupBy);
            }

            if($groupBy && $orderBy) {
                $orderBy = array_combine(array_map(static function ($value){
                    $array = explode('.', $value);
                    return end($array);
                }, array_keys($orderBy)), $orderBy);

                $query->orderBy($orderBy);
            }
        }

        return $query;
    }

    /**
     * alap scope-ot tartalmazhatja
     *
     * @return ActiveQuery
     */
    abstract protected function query();


    /**
     * @param bool $wrapQuery
     * @return ActiveRecord
     */
    public function wrapQuery(bool $wrapQuery)
    {
        $this->wrapQuery = $wrapQuery;

        return $this;
    }
}
