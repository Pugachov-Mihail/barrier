<?php

namespace app\models;
use yii\base\Model;
use yii\web\UploadedFile;

//Загрузка csv файла и сохранение его в БД
class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $csvFile;

    public function rules()
    {
        return [
            [['csvFile'], 'file',
                'extensions' => 'csv',
                'checkExtensionByMimeType' => false,
                'skipOnEmpty' => false,
            ],
        ];
    }

    public function upload(){
        if ($this->validate()){
            $this->csvFile->saveAs('upload/' . $this->csvFile->baseName . '.' . $this->csvFile->extension);
            return true;
        }else {
            return false;
        }
    }
    //Парсинг csv файла
    public function parsingCsvFile($csvFile){
        $returnArray = [
            'number' => '',
            'name'=>''
        ];
        $csv = fopen(\Yii::getAlias('@app/web/upload/') . $csvFile, 'r');
        while(($csvArray = fgetcsv($csv, 1000, ';', ' ', ' '))!==false){
            $up_text = mb_strtoupper($csvArray[0]);
            $key_array = ['А', 'В', 'Е', 'Х', 'К', 'М','Н','О','Р','С','Т','У'];
            $value_arr = ["A","B","E","X","K","M","H","O","P","C","T","Y"];
            $num = str_replace($key_array, $value_arr, $up_text,);
            $returnArray['number'] = $num;
            $returnArray['name'] = $csvArray[1];
        };
        fclose($csv);
        return $num;
    }

    static function saveDataCsvFileDb($number, $name, $model=0){
            print_r($name . " " . $number);
            return "dsadsa";
    }
}