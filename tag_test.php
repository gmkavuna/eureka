<link type="text/css" rel="stylesheet" rev="stylesheet" href="css/tag_styles.css" />
<?php
include("include/tag_cloud.inc");
$randomWords = array(
                     "webmasterworld", "Computer", "Skateboarding", "PC", 
                     "music", "music", "music", 
                     "music", "PHP", "C", "XHTML", "eminem", 
                     "programming", "forums", "webmasterworld",
                     "Chill out", "email", "forums", "Computer", "GTA", "css", "mysql", 
                     "sql", "css", "mysql", "sql",
                     "forums", "internet", "class", "object", "method", "music", "music", 
                     "music", "music", "gui", "encryption"
                     );

$cloud = new wordCloud($randomWords);
$cloud->addWord("music", 12);
$cloud->addWord("downloads", 8);
$cloud->addWord("internet", 17);
$cloud->addWord("PHP", 22);
$cloud->addWord("CSS", 32);
echo $cloud->showCloud()

?>