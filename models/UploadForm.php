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
    public function generateQueryDB($csvFile){
        $csv = fopen(\Yii::getAlias('@app/web/upload/') . $csvFile, 'r');
       // fputs($csv, chr(0xEF) . chr(0xBB) . chr(0xBF));
        while(($csvArray = fgetcsv($csv, 1000, ';', ' ', ' '))!==false){
            foreach ($csvArray as $value) {
                $c = mb_convert_encoding($value, "IBM866", "Windows-1251");
                //iconv("ASCII", "UTF-8", $value);
                $a = mb_detect_encoding($c);
                print_r($c);
                print_r($a);
                echo '<hr>';
            }
        };
        fclose($csv);

        return;
    }
}