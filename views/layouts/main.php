<?php

/** @var yii\web\View $this */
/** @var string $content */

$pages = Yii::$app->request->get('pages');
use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => 'Шлагбаум',
        'brandUrl' => ['/device/index', 'pages'=>$pages],
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            ['label' => 'Характеристики устройства', 'url' => ['/device/index', 'pages' => $pages]],
            ['label' => 'Списки посетителей', 'url' => ['/device/debtor-list', 'pages' => $pages]],
            ['label' => 'Авторизация устройства', 'url' => ['/device/authorization', 'pages' => $pages]],
    //        ['label' => 'Добавить посетителя', 'url' => ['/device/add-new-guest', 'pages'=>$pages]],
            Yii::$app->user->isGuest
                ? ['label' => 'Авторизация', 'url' => ['/device/login', 'pages'=>$pages]]
                : '<li class="nav-item">'
                    . Html::beginForm(['/device/logout', 'pages'=>$pages])
                    . Html::submitButton(
                        'Выйти (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'nav-link btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>'
        ]
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>



<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
