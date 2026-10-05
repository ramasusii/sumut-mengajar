<?php
namespace app\models;

use yii\base\Model;

class PublicTrackingForm extends Model
{
    public $application_code;

    private ?Application $_application = null;

    public function rules()
    {
        return [
            [['application_code'], 'required', 'message' => 'Masukkan kode pendaftaran.'],
            [['application_code'], 'string', 'max' => 40],
            [['application_code'], 'filter', 'filter' => static function ($value) {
                $value = mb_strtoupper(trim((string)$value));
                $value = preg_replace('/\s+/', '', $value);
                return $value;
            }],
            [['application_code'], 'match',
                'pattern' => '/^GSM-[A-Z0-9\-]+$/',
                'message' => 'Format kode pendaftaran tidak sesuai.'
            ],
            [['application_code'], 'validateApplicationCode'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'application_code' => 'Kode Pendaftaran',
        ];
    }

    public function validateApplicationCode($attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $this->_application = Application::find()
            ->where(['application_code' => $this->application_code])
            ->with(['batch.kabupatenKota'])
            ->one();

        if (!$this->_application) {
            $this->addError(
                $attribute,
                'Kode pendaftaran tidak ditemukan. Periksa kembali kode yang kamu masukkan.'
            );
        }
    }

    public function getApplication(): ?Application
    {
        if ($this->_application !== null) {
            return $this->_application;
        }

        if (!$this->application_code) {
            return null;
        }

        $this->_application = Application::find()
            ->where(['application_code' => $this->application_code])
            ->with(['batch.kabupatenKota'])
            ->one();

        return $this->_application;
    }
}
