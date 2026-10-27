<?php
/*
    كان هذا الملف يعرّف IsSpeed() لإرسال صفحة مختلفة لأدوات القياس (Lighthouse/PageSpeed)
    ولموبايلات "moto g". أُلغي ذلك: الجميع يرى نفس الصفحة.
    تبقى الدالة وتُرجع false دائمًا فقط حتى لا يتعطل أي كود خارجي (مثل مقتطفات WPCode) قد يستدعيها.
*/
if ( ! function_exists( 'IsSpeed' ) ) {
    function IsSpeed() {
        return false;
    }
}
