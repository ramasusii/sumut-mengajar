<?php
use yii\helpers\Html;

$this->title = $model->title;
$this->registerCss(<<<CSS
.article-detail{padding:54px 0 90px;background:#fff}.article-detail-wrap{max-width:860px;margin:auto}.article-detail-meta{font-size:11px;color:#16824a;font-weight:800;letter-spacing:.6px}.article-detail h1{font-size:44px;line-height:1.15;margin:10px 0 14px}.article-detail-excerpt{font-size:17px;line-height:1.7;color:#66756c}.article-detail-cover{margin:28px 0;border-radius:24px;overflow:hidden}.article-detail-cover img{width:100%;display:block}.article-content{font-size:15px;line-height:1.9;color:#28352e;white-space:pre-line}@media(max-width:650px){.article-detail h1{font-size:32px}}
CSS);
?>
<section class="article-detail">
    <div class="container">
        <article class="article-detail-wrap">
            <div class="article-detail-meta">
                <?= Html::encode($model->category ? $model->category->name : 'Artikel') ?>
                <?php if($model->published_at): ?> · <?= Yii::$app->formatter->asDate($model->published_at) ?><?php endif; ?>
            </div>
            <h1><?= Html::encode($model->title) ?></h1>
            <?php if($model->excerpt): ?><p class="article-detail-excerpt"><?= Html::encode($model->excerpt) ?></p><?php endif; ?>
            <?php if($model->cover_image): ?>
                <div class="article-detail-cover">
                    <img src="<?= Html::encode(Yii::$app->request->baseUrl . '/web/' . ltrim($model->cover_image,'/')) ?>" alt="<?= Html::encode($model->title) ?>">
                </div>
            <?php endif; ?>
            <div class="article-content"><?= Html::encode($model->content ?: '') ?></div>
        </article>
    </div>
</section>
