<?php declare(strict_types=1);

use App\Feature\Demo\Entity\Model\DemoModel;
use App\Feature\Demo\Presenter\DemoPageViewModel;
use SmallFramework\Core\Entity\Service\HtmlService as Html;

?>
<h2>demo page</h2>

<h3>fetching tables demo</h3>
<?php

/** @var DemoPageViewModel $viewModel */
/** @var DemoModel $demo */
foreach ($viewModel->getDemos() as $demo) {
    printf("get_class: %s<br>\n", Html::escape(get_class($demo)));
    printf("title: %s<br>\n", Html::escape($demo->title));
    printf("<pre>var_export: %s</pre><br>\n", Html::escape(var_export($demo, true)));
}
?>
