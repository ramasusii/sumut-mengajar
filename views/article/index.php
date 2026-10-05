<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Artikel & Cerita';
$this->registerCss(<<<CSS
.article-page{padding:58px 0 80px;background:#f7faf8}.article-heading{max-width:760px;margin-bottom:30px}.article-heading h1{font-size:42px;margin:8px 0 10px}.article-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}.article-card{background:#fff;border:1px solid #e4ebe6;border-radius:20px;overflow:hidden;display:flex;flex-direction:column}.article-cover{aspect-ratio:16/10;background:#edf3ef;overflow:hidden}.article-cover img{width:100%;height:100%;object-fit:cover}.article-body{padding:20px;display:flex;flex-direction:column;flex:1}.article-meta{font-size:10px;font-weight:800;letter-spacing:.8px;color:#16824a}.article-body h2{font-size:20px;line-height:1.3;margin:8px 0}.article-body p{font-size:12px;color:#6d7a72;line-height:1.6}.article-body a{margin-top:auto;color:#0e623a;font-weight:800;text-decoration:none}@media(max-width:900px){.article-grid{grid-template-columns:1fr 1fr}}@media(max-width:600px){.article-grid{grid-template-columns:1fr}.article-heading h1{font-size:32px}}
CSS);
?>
<section class="article-page">
    <div class="container">
        <div class="article-heading">
            <span class="section-kicker">CERITA & INFORMASI</span>
            <h1>Artikel Sumut Mengajar</h1>
            <p>Berita kegiatan, cerita pengabdian, dan kabar terbaru Gerakan Sumut Mengajar.</p>
        </div>

        <div class="article-grid">
            <?php foreach($models as $model): ?>
                <article class="article-card">
                    <div class="article-cover">
                        <?php if($model->cover_image): ?>
                            <img src="<?= Html::encode(Yii::$app->request->baseUrl . '/web/' . ltrim($model->cover_image,'/')) ?>" alt="<?= Html::encode($model->title) ?>">
                        <?php endif; ?>
                    </div>
                    <div class="article-body">
                        <div class="article-meta">
                            <?= Html::encode($model->category ? $model->category->name : 'Artikel') ?>
                            <?php if($model->published_at): ?> · <?= Yii::$app->formatter->asDate($model->published_at) ?><?php endif; ?>
                        </div>
                        <h2><?= Html::encode($model->title) ?></h2>
                        <p><?= Html::encode($model->excerpt ?: mb_strimwidth(strip_tags((string)$model->content),0,160,'…')) ?></p>
                        <a href="<?= Url::to(['/article/view','slug'=>$model->slug]) ?>">Baca Selengkapnya →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if(!$models): ?><p>Belum ada artikel yang diterbitkan.</p><?php endif; ?>
        <?= LinkPager::widget(['pagination'=>$pagination]) ?>
    </div>
</section>
