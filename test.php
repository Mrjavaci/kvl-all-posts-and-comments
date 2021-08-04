<?php
$myHTML = "&nbsp;abc";

$converted = strtr($myHTML, array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES)));

echo $converted;