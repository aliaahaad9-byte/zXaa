<?php
global $ThemeTree; // التأكد من أن المتغير $ThemeTree معرف كـ global

if ($ThemeTree != null) {
    $ThemeTree->TemplatePart('home'); // استدعاء دالة TemplatePart()
   
} 
?>
