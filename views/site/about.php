<?php
$this->title = 'Tentang Sumut Mengajar';

$logoMaknaUrl = Yii::$app->request->baseUrl . '/web/assets-guest/img/logo-makna.jpg';

$structureImageRelative = 'web/assets-guest/img/struktur-organisasi.jpg';
$structureImageFile = Yii::getAlias('@app') . '/' . $structureImageRelative;
$structureImageUrl = Yii::$app->request->baseUrl . '/' . $structureImageRelative;
$hasStructureImage = is_file($structureImageFile);
?>

<section class="page-hero about-hero">
    <div class="container">
        <span class="section-kicker">TENTANG KAMI</span>
        <h1>Gerakan Sumut Mengajar</h1>
        <p>Aksi Nyata Peduli Pendidikan Sumatera Utara. Sekali Mengabdi, Selamanya Menginspirasi.</p>
    </div>
</section>

<section class="section gsm-about-intro-v227">
    <div class="container">
        <div class="gsm-about-intro-v227__grid">
            <div class="gsm-about-intro-v227__title">
                <span class="section-kicker">GERAKAN KAMI</span>
                <h2>Belajar, berbagi, menginspirasi, peduli, dan bahagia.</h2>
                <div class="gsm-about-intro-v227__line"></div>
            </div>

            <div class="gsm-about-intro-v227__copy">
                <p>
                    Sumut Mengajar merupakan gerakan kepedulian pendidikan yang mempertemukan
                    relawan muda dan masyarakat dalam aksi pengabdian di berbagai daerah Sumatera Utara.
                </p>
                <p>
                    Gerakan ini tidak hanya hadir untuk menyampaikan ilmu, tetapi juga menumbuhkan
                    kepedulian, menciptakan inspirasi, dan menebar kebahagiaan melalui pengalaman
                    belajar bersama.
                </p>

                <div class="gsm-about-intro-v227__values">
                    <span>Belajar</span>
                    <span>Berbagi</span>
                    <span>Menginspirasi</span>
                    <span>Peduli</span>
                    <span>Bahagia</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- <section class="section soft gsm-structure-v227">
    <div class="container">
        <div class="gsm-structure-v227__head">
            <div>
                <span class="section-kicker">ORGANISASI</span>
                <h2>Struktur Organisasi</h2>
            </div>
            <p>
                Struktur organisasi membantu memperlihatkan peran dan koordinasi tim
                dalam menjalankan program, pengabdian, dan keberlanjutan Gerakan Sumut Mengajar.
            </p>
        </div>

        <div class="gsm-structure-v227__frame <?= $hasStructureImage ? 'has-image' : 'is-placeholder' ?>">
            <?php if ($hasStructureImage): ?>
                <img
                    src="<?= $structureImageUrl ?>"
                    alt="Struktur Organisasi Gerakan Sumut Mengajar"
                    loading="lazy"
                >
            <?php else: ?>
                <div class="gsm-structure-v227__placeholder">
                    <div class="gsm-structure-v227__icon">SM</div>
                    <strong>Area Foto Struktur Organisasi</strong>
                    <span>Foto struktur organisasi akan ditampilkan pada area ini.</span>
                    <small>Siapkan file: <b>struktur-organisasi.jpg</b></small>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section> -->

<section class="section soft identity-section">
    <div class="container">
        <div class="identity-head">
            <div>
                <span class="section-kicker">IDENTITAS GERAKAN</span>
                <h2>Makna Logo Sumut Mengajar</h2>
            </div>
            <p>Identitas visual ini menjadi simbol perjalanan, semangat, dan nilai yang dibawa Gerakan Sumut Mengajar.</p>
        </div>

        <div class="identity-grid">
            <figure class="identity-poster">
                <img src="<?= $logoMaknaUrl ?>" alt="Makna Logo Sumut Mengajar">
            </figure>

            <div class="identity-content">
                <div class="identity-lead">
                    <span>10 TAHUN PERJALANAN</span>
                    <h3>Sebuah simbol perjalanan dan gerakan kolektif.</h3>
                    <p>Logo Sumut Mengajar menjadi simbol perjalanan Gerakan Sumut Mengajar yang berlandaskan semangat <strong>Belajar, Berbagi, Menginspirasi, Peduli, dan Bahagia</strong>.</p>
                </div>

                <div class="meaning-list">
                    <article><b>01</b><div><h4>Angka 1</h4><p>Warna gradasi oranye melambangkan tonggak perjalanan satu dekade, sekaligus keteguhan dan komitmen.</p></div></article>
                    <article><b>02</b><div><h4>Angka 0</h4><p>Bentuk lingkaran mencerminkan kebersamaan, kesinambungan, dan semangat yang tidak terputus dalam mendidik generasi bangsa.</p></div></article>
                    <article><b>03</b><div><h4>Figur dengan topi toga</h4><p>Melambangkan semangat belajar yang terus tumbuh dan menginspirasi banyak orang.</p></div></article>
                    <article><b>04</b><div><h4>Daun hijau</h4><p>Mencerminkan kepedulian terhadap lingkungan, keberlanjutan, dan tumbuh kembang generasi penerus.</p></div></article>
                    <article><b>05</b><div><h4>Warna oranye</h4><p>Melambangkan semangat, optimisme, energi, dan rasa bahagia dalam setiap langkah pengabdian.</p></div></article>
                    <article><b>06</b><div><h4>Warna hijau</h4><p>Mencerminkan keseimbangan, kepedulian, dan harapan baru bagi masa depan generasi Sumatera Utara dan Indonesia.</p></div></article>
                </div>

                <div class="identity-slogan">
                    <small>SLOGAN</small>
                    <strong>Bergerak Cerdaskan Negeri</strong>
                    <p>Menegaskan bahwa Sumut Mengajar bukan hanya sebuah program, tetapi aksi nyata yang bergerak bersama masyarakat untuk mencerdaskan bangsa.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$this->registerCss(<<<'CSS'
/* TENTANG V22.7 */
.gsm-about-intro-v227{
    padding-top:72px!important;
    padding-bottom:72px!important;
}
.gsm-about-intro-v227__grid{
    display:grid!important;
    grid-template-columns:minmax(0,.92fr) minmax(0,1.08fr)!important;
    gap:72px!important;
    align-items:start!important;
    max-width:1120px;
    margin:0 auto;
}
.gsm-about-intro-v227__title,
.gsm-about-intro-v227__copy{
    min-width:0;
}
.gsm-about-intro-v227__title h2{
    max-width:520px;
    margin:10px 0 0;
    color:#1f2d25;
    font-size:clamp(34px,4vw,54px);
    line-height:1.08;
    letter-spacing:-1.8px;
}
.gsm-about-intro-v227__line{
    width:72px;
    height:3px;
    margin-top:24px;
    border-radius:999px;
    background:#16824a;
}
.gsm-about-intro-v227__copy{
    padding-top:29px;
}
.gsm-about-intro-v227__copy p{
    max-width:600px;
    margin:0 0 15px;
    color:#647168;
    font-size:15px;
    line-height:1.8;
}
.gsm-about-intro-v227__values{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
    margin-top:24px;
}
.gsm-about-intro-v227__values span{
    display:inline-flex;
    align-items:center;
    min-height:32px;
    padding:0 12px;
    border:1px solid #d9e6dc;
    border-radius:999px;
    background:#f7fbf8;
    color:#356048;
    font-size:10px;
    font-weight:850;
    letter-spacing:.25px;
}

/* STRUKTUR ORGANISASI */
.gsm-structure-v227{
    padding-top:74px!important;
    padding-bottom:84px!important;
    background:#f7f9f6!important;
}
.gsm-structure-v227__head{
    display:grid;
    grid-template-columns:minmax(0,.9fr) minmax(0,1.1fr);
    align-items:end;
    gap:64px;
    max-width:1120px;
    margin:0 auto 28px;
}
.gsm-structure-v227__head h2{
    margin:8px 0 0;
    color:#1f2d25;
    font-size:clamp(30px,3.2vw,44px);
    line-height:1.08;
    letter-spacing:-1.2px;
}
.gsm-structure-v227__head>p{
    max-width:560px;
    margin:0;
    color:#6d7971;
    font-size:13px;
    line-height:1.75;
}
.gsm-structure-v227__frame{
    position:relative;
    width:min(1120px,100%);
    margin:0 auto;
    overflow:hidden;
    border:1px solid #dfe7e1;
    border-radius:24px;
    background:#fff;
    box-shadow:0 18px 44px rgba(26,57,39,.06);
}
.gsm-structure-v227__frame.has-image{
    padding:22px;
}
.gsm-structure-v227__frame.has-image img{
    display:block;
    width:100%;
    height:auto;
    max-height:860px;
    object-fit:contain;
    border-radius:15px;
    background:#fff;
}
.gsm-structure-v227__frame.is-placeholder{
    display:grid;
    place-items:center;
    min-height:430px;
    padding:42px 24px;
    background:
        linear-gradient(135deg,rgba(22,130,74,.035),rgba(244,174,55,.045)),
        #fff;
}
.gsm-structure-v227__placeholder{
    display:flex;
    flex-direction:column;
    align-items:center;
    max-width:500px;
    text-align:center;
}
.gsm-structure-v227__icon{
    display:grid;
    place-items:center;
    width:72px;
    height:72px;
    margin-bottom:16px;
    border-radius:22px;
    background:#e9f5ed;
    color:#0e623a;
    font-size:22px;
    font-weight:900;
}
.gsm-structure-v227__placeholder strong{
    color:#28382f;
    font-size:18px;
}
.gsm-structure-v227__placeholder span{
    margin-top:6px;
    color:#748078;
    font-size:12px;
    line-height:1.6;
}
.gsm-structure-v227__placeholder small{
    margin-top:12px;
    padding:7px 10px;
    border-radius:8px;
    background:#f2f5f3;
    color:#89938d;
    font-size:10px;
}
.gsm-structure-v227__placeholder small b{
    color:#4f5f55;
}

@media(max-width:820px){
    .gsm-about-intro-v227{
        padding-top:52px!important;
        padding-bottom:56px!important;
    }
    .gsm-about-intro-v227__grid,
    .gsm-structure-v227__head{
        grid-template-columns:1fr!important;
        gap:28px!important;
    }
    .gsm-about-intro-v227__copy{
        padding-top:0;
    }
    .gsm-structure-v227{
        padding-top:58px!important;
        padding-bottom:64px!important;
    }
    .gsm-structure-v227__frame.is-placeholder{
        min-height:320px;
    }
}
CSS);
?>
