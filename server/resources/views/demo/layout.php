<?php
declare(strict_types=1);
use SmallFramework\Core\Entity\Service\HtmlService as Html;

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
<title><?php echo !isset($title) ? '' : Html::escape($title); ?></title>
<?php echo implode("\n", $this->tagsForAddToHtmlHeader)."\n"; ?>

</head>
<body>
    <?php echo $this->generatePartialHtml('header'); ?>
    <?php echo $this->generatePartialHtml('main'); ?>
    <?php echo $this->generatePartialHtml('footer'); ?>
</body>
</html>
