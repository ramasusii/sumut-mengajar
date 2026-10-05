<?php
namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class RecruitmentBatch extends ActiveRecord
{
    public const STATUS_DRAFT='draft';
    public const STATUS_OPEN='open';
    public const STATUS_CLOSED='closed';
    public const STATUS_ANNOUNCED='announced';

    /** @var int[] Lokasi pengabdian yang dipilih pada form admin. */
    public $location_ids = [];

    public static function tableName(){ return 'recruitment_batch'; }
    public function behaviors(){ return [TimestampBehavior::className()]; }

    public function rules()
    {
        return [
            [['code','title','slug','batch_number','registration_start','registration_end','status'], 'required'],
            [['location_ids'], 'required', 'message' => 'Pilih minimal satu lokasi pengabdian.'],
            [['location_ids'], 'each', 'rule' => ['integer']],
            [['kabupaten_kota_id','chapter_id','batch_number','quota','created_by','created_at','updated_at'], 'integer'],
            [['description','requirements','benefits'], 'string'],
            [['registration_start','registration_end','interview_date','briefing_date','activity_start','activity_end','announcement_date'], 'safe'],
            [['code'], 'string','max'=>30], [['title'], 'string','max'=>180], [['slug'], 'string','max'=>190], [['banner'], 'string','max'=>255],
            [['status'], 'in', 'range'=>[self::STATUS_DRAFT,self::STATUS_OPEN,self::STATUS_CLOSED,self::STATUS_ANNOUNCED]],
            [['code'], 'unique'], [['slug'], 'unique'],
            [['registration_end'], 'validateTimeline'],
        ];
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->location_ids = [];

        if ($this->hasLocationTable()) {
            $this->location_ids = array_map('intval', $this->getLocations()->select('kabupaten_kota.id')->column());
        }

        if (!$this->location_ids && $this->kabupaten_kota_id) {
            $this->location_ids = [(int)$this->kabupaten_kota_id];
        }
    }

    public function validateTimeline($attribute)
    {
        if ($this->registration_start && $this->registration_end && $this->registration_end < $this->registration_start) {
            $this->addError('registration_end', 'Tanggal tutup pendaftaran tidak boleh sebelum tanggal mulai.');
        }
        if ($this->activity_start && $this->activity_end && $this->activity_end < $this->activity_start) {
            $this->addError('activity_end', 'Tanggal selesai pengabdian tidak boleh sebelum tanggal mulai.');
        }
        if ($this->registration_end && $this->interview_date && $this->interview_date < $this->registration_end) {
            $this->addError('interview_date', 'Tanggal wawancara sebaiknya setelah pendaftaran ditutup.');
        }
        if ($this->interview_date && $this->announcement_date && $this->announcement_date < $this->interview_date) {
            $this->addError('announcement_date', 'Tanggal pengumuman tidak boleh sebelum wawancara.');
        }
        if ($this->announcement_date && $this->briefing_date && $this->briefing_date < $this->announcement_date) {
            $this->addError('briefing_date', 'Tanggal pembekalan tidak boleh sebelum pengumuman.');
        }
        if ($this->briefing_date && $this->activity_start && $this->activity_start < $this->briefing_date) {
            $this->addError('activity_start', 'Tanggal mulai pengabdian tidak boleh sebelum pembekalan.');
        }
        if ($this->announcement_date && $this->activity_start && $this->activity_start < $this->announcement_date) {
            $this->addError('activity_start', 'Tanggal mulai pengabdian tidak boleh sebelum pengumuman.');
        }
    }

    public function getKabupatenKota(){ return $this->hasOne(KabupatenKota::class, ['id'=>'kabupaten_kota_id']); }

    public function getLocations()
    {
        return $this->hasMany(KabupatenKota::class, ['id' => 'kabupaten_kota_id'])
            ->viaTable('recruitment_batch_location', ['batch_id' => 'id'])
            ->orderBy(['kabupaten_kota.nama' => SORT_ASC]);
    }

    public function getLocationLabel(): string
    {
        $rows = [];
        if ($this->hasLocationTable()) {
            try { $rows = $this->locations; } catch (\Throwable $e) { $rows = []; }
        }

        if ($rows) {
            return implode(', ', array_map(static fn($region) => $region->label, $rows));
        }

        return $this->kabupatenKota ? $this->kabupatenKota->label : 'Sumatera Utara';
    }

    public function hasLocationTable(): bool
    {
        try {
            return Yii::$app->db->schema->getTableSchema('recruitment_batch_location', true) !== null;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getChapter(){ return $this->hasOne(Chapter::class, ['id'=>'chapter_id']); }
    public function getApplications(){ return $this->hasMany(Application::class, ['batch_id'=>'id']); }

    public function isOpen(): bool
    {
        $today=date('Y-m-d');
        return $this->status===self::STATUS_OPEN && $this->registration_start <= $today && $this->registration_end >= $today;
    }
}
