<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class DeviceSearch extends Device
{
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($param)
    {
        $device = Device::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $device,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ]
        ]);

        $this->load($param);

        $device->andFilterWhere(['company_id'=>$this->company_id]);

        if(!$this->validate()){
            return $dataProvider;
        }

        return $dataProvider;
    }
}