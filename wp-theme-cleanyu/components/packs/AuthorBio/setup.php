<?php
/**
 * ملف الخبير — حقول الملف الشخصي (معايير E-E-A-T) وأدوات القراءة.
 *
 * يضيف إلى صفحة «الملف الشخصي» في لوحة التحكم نموذجًا منظمًا:
 * التعريف الأساسي، الخبرة والأرقام، الخبرة حسب القسم، الشهادات،
 * العضويات، سجل الإنجازات، والحسابات المهنية.
 *
 * كل ما تعرضه صفحة الكاتب وصندوق «نبذة عن الكاتب» يُقرأ من
 * الدالة yc_author_profile() فقط.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* =========================================================================
 * الأيقونات
 * ====================================================================== */

/** أيقونات مجالات الخبرة: المفتاح => [التسمية، viewBox، المسار]. */
function yc_author_icons() {
    return array(
        'cleaning'   => array( 'تنظيف', '0 0 576 512', '<path d="M566.6 54.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192-34.7-34.7c-4.2-4.2-10-6.6-16-6.6c-12.5 0-22.6 10.1-22.6 22.6v29.1L364.3 320h29.1c12.5 0 22.6-10.1 22.6-22.6c0-6-2.4-11.8-6.6-16l-34.7-34.7 192-192zM341.1 353.4L222.6 234.9c-42.7-3.7-85.2 11.7-115.8 42.3l-8 8C76.5 307.5 64 337.7 64 369.2c0 6.8 7.1 11.2 13.2 8.2l51.1-25.5c5-2.5 9.5 4.1 5.4 7.9L7.3 473.4C2.7 477.6 0 483.6 0 489.9C0 502.1 9.9 512 22.1 512l173.3 0c38.8 0 75.9-15.4 103.4-42.8c30.6-30.6 45.9-73.1 42.3-115.8z"/>' ),
        'pest'       => array( 'مكافحة حشرات', '0 0 512 512', '<path d="M256 0c53 0 96 43 96 96v3.6c0 15.7-12.7 28.4-28.4 28.4H188.4c-15.7 0-28.4-12.7-28.4-28.4V96c0-53 43-96 96-96zM41.4 105.4c12.5-12.5 32.8-12.5 45.3 0l64 64c.7 .7 1.3 1.4 1.9 2.1c14.2-7.3 30.4-11.4 47.5-11.4H312c17.1 0 33.2 4.1 47.5 11.4c.6-.7 1.2-1.4 1.9-2.1l64-64c12.5-12.5 32.8-12.5 45.3 0s12.5 32.8 0 45.3l-64 64c-.7 .7-1.4 1.3-2.1 1.9c6.2 12 10.1 25.3 11.1 39.5H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H416c0 24.6-5.5 47.8-15.4 68.6c2.2 1.3 4.2 2.9 6 4.8l64 64c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0l-63.1-63.1c-24.5 21.8-55.8 36.2-90.3 39.6V240c0-8.8-7.2-16-16-16s-16 7.2-16 16V479.2c-34.5-3.4-65.8-17.8-90.3-39.6L86.6 502.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l64-64c1.9-1.9 3.9-3.4 6-4.8C101.5 367.8 96 344.6 96 320H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H96.3c1.1-14.1 5-27.5 11.1-39.5c-.7-.6-1.4-1.2-2.1-1.9l-64-64c-12.5-12.5-12.5-32.8 0-45.3z"/>' ),
        'leak'       => array( 'كشف تسربات', '0 0 384 512', '<path d="M192 512C86 512 0 426 0 320C0 228.8 130.2 57.7 166.6 11.7C172.6 4.2 181.5 0 191.1 0h1.8c9.6 0 18.5 4.2 24.5 11.7C253.8 57.7 384 228.8 384 320c0 106-86 192-192 192zM96 336c0-8.8-7.2-16-16-16s-16 7.2-16 16c0 61.9 50.1 112 112 112c8.8 0 16-7.2 16-16s-7.2-16-16-16c-44.2 0-80-35.8-80-80z"/>' ),
        'insulation' => array( 'عزل', '0 0 512 512', '<path d="M256 0c4.6 0 9.2 1 13.4 2.9L457.7 82.8c22 9.3 38.4 31 38.3 57.2c-.5 99.2-41.3 280.7-213.6 363.2c-16.7 8-36.1 8-52.8 0C57.3 420.7 16.5 239.2 16 140c-.1-26.2 16.3-47.9 38.3-57.2L242.7 2.9C246.8 1 251.4 0 256 0zm0 66.8V444.8C394 378 431.1 230.1 432 141.4L256 66.8l0 0z"/>' ),
        'home'       => array( 'منازل', '0 0 576 512', '<path d="M575.8 255.5c0 18-15 32.1-32 32.1h-32l.7 160.2c0 2.7-.2 5.4-.5 8.1V472c0 22.1-17.9 40-40 40H456c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1H416 392c-22.1 0-40-17.9-40-40V448 384c0-17.7-14.3-32-32-32H256c-17.7 0-32 14.3-32 32v64 24c0 22.1-17.9 40-40 40H160 128.1c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2H104c-22.1 0-40-17.9-40-40V360c0-.9 0-1.9 .1-2.8V287.6H32c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z"/>' ),
        'tools'      => array( 'صيانة عامة', '0 0 512 512', '<path d="M352 320c88.4 0 160-71.6 160-160c0-15.3-2.2-30.1-6.2-44.2c-3.1-10.8-16.4-13.2-24.3-5.3l-76.8 76.8c-3 3-7.1 4.7-11.3 4.7H336c-8.8 0-16-7.2-16-16V118.6c0-4.2 1.7-8.3 4.7-11.3l76.8-76.8c7.9-7.9 5.4-21.2-5.3-24.3C382.1 2.2 367.3 0 352 0C263.6 0 192 71.6 192 160c0 19.1 3.4 37.5 9.5 54.5L19.9 396.1C7.2 408.8 0 426.1 0 444.1C0 481.6 30.4 512 67.9 512c18 0 35.3-7.2 48-19.9L297.5 310.5c17 6.2 35.4 9.5 54.5 9.5zM80 408a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/>' ),
    );
}

/** أيقونات الواجهة العامة (ليست خيارات للمجالات). */
function yc_author_ui_icons() {
    return array(
        'user'    => array( '0 0 448 512', '<path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/>' ),
        'bag'     => array( '0 0 512 512', '<path d="M184 48H328c4.4 0 8 3.6 8 8V96H176V56c0-4.4 3.6-8 8-8zm-56 8V96H64C28.7 96 0 124.7 0 160v96H192 320 512V160c0-35.3-28.7-64-64-64H384V56c0-30.9-25.1-56-56-56H184c-30.9 0-56 25.1-56 56zM512 288H320v32c0 17.7-14.3 32-32 32H224c-17.7 0-32-14.3-32-32V288H0V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V288z"/>' ),
        'clip'    => array( '0 0 384 512', '<path d="M192 0c-41.8 0-77.4 26.7-90.5 64H64C28.7 64 0 92.7 0 128V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H282.5C269.4 26.7 233.8 0 192 0zm0 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64zM305 273L177 401c-9.4 9.4-24.6 9.4-33.9 0L79 337c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L271 239c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>' ),
        'news'    => array( '0 0 512 512', '<path d="M96 96c0-35.3 28.7-64 64-64H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H80c-44.2 0-80-35.8-80-80V128c0-17.7 14.3-32 32-32s32 14.3 32 32V400c0 8.8 7.2 16 16 16s16-7.2 16-16V96zm64 24v80c0 13.3 10.7 24 24 24H296c13.3 0 24-10.7 24-24V120c0-13.3-10.7-24-24-24H184c-13.3 0-24 10.7-24 24zm208-8c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zM160 304c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16z"/>' ),
        'award'   => array( '0 0 384 512', '<path d="M173.8 5.5c11-7.3 25.4-7.3 36.4 0L228 17.2c6 3.9 13 5.8 20.1 5.4l21.3-1.3c13.2-.8 25.6 6.4 31.5 18.2l9.6 19.1c3.2 6.4 8.4 11.5 14.7 14.7L344.5 83c11.8 5.9 19 18.3 18.2 31.5l-1.3 21.3c-.4 7.1 1.5 14.2 5.4 20.1l11.8 17.8c7.3 11 7.3 25.4 0 36.4L366.8 228c-3.9 6-5.8 13-5.4 20.1l1.3 21.3c.8 13.2-6.4 25.6-18.2 31.5l-19.1 9.6c-6.4 3.2-11.5 8.4-14.7 14.7L301 344.5c-5.9 11.8-18.3 19-31.5 18.2l-21.3-1.3c-7.1-.4-14.2 1.5-20.1 5.4l-17.8 11.8c-11 7.3-25.4 7.3-36.4 0L156 366.8c-6-3.9-13-5.8-20.1-5.4l-21.3 1.3c-13.2 .8-25.6-6.4-31.5-18.2l-9.6-19.1c-3.2-6.4-8.4-11.5-14.7-14.7L39.5 301c-11.8-5.9-19-18.3-18.2-31.5l1.3-21.3c.4-7.1-1.5-14.2-5.4-20.1L5.5 210.2c-7.3-11-7.3-25.4 0-36.4L17.2 156c3.9-6 5.8-13 5.4-20.1l-1.3-21.3c-.8-13.2 6.4-25.6 18.2-31.5l19.1-9.6C65 70.2 70.2 65 73.4 58.6L83 39.5c5.9-11.8 18.3-19 31.5-18.2l21.3 1.3c7.1 .4 14.2-1.5 20.1-5.4L173.8 5.5zM272 192a80 80 0 1 0 -160 0 80 80 0 1 0 160 0zM1.3 441.8L44.4 339.3c.2 .1 .3 .2 .4 .4l9.6 19.1c11.7 23.2 36 37.3 62 35.8l21.3-1.3c.2 0 .5 0 .7 .2l17.8 11.8c5.1 3.3 10.5 5.9 16.1 7.7l-37.6 89.3c-2.3 5.5-7.4 9.2-13.3 9.7s-11.6-2.2-14.8-7.2L74.4 455.5l-56.1 8.3c-5.7 .8-11.4-1.5-15-6s-4.3-10.7-2.1-16zm248 60.4L211.7 413c5.6-1.8 11-4.3 16.1-7.7l17.8-11.8c.2-.1 .4-.2 .7-.2l21.3 1.3c26 1.5 50.3-12.6 62-35.8l9.6-19.1c.1-.2 .2-.3 .4-.4l43.2 102.5c2.2 5.3 1.4 11.4-2.1 16s-9.3 6.9-15 6l-56.1-8.3-32.2 49.2c-3.2 5-8.9 7.7-14.8 7.2s-11-4.3-13.3-9.7z"/>' ),
        'people'  => array( '0 0 640 512', '<path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z"/>' ),
        'bank'    => array( '0 0 512 512', '<path d="M243.4 2.6l-224 96c-14 6-21.8 21-18.7 35.8S16.8 160 32 160v8c0 13.3 10.7 24 24 24H456c13.3 0 24-10.7 24-24v-8c15.2 0 28.3-10.7 31.3-25.6s-4.8-29.9-18.7-35.8l-224-96c-8-3.4-17.2-3.4-25.2 0zM128 224H64V420.3c-.6 .3-1.2 .7-1.8 1.1l-48 32c-11.7 7.8-17 22.4-12.9 35.9S17.9 512 32 512H480c14.1 0 26.5-9.2 30.6-22.7s-1.1-28.1-12.9-35.9l-48-32c-.6-.4-1.2-.7-1.8-1.1V224H384V416H344V224H280V416H232V224H168V416H128V224zM256 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/>' ),
        'seal'    => array( '0 0 512 512', '<path d="M211 7.3C205 1 196-1.4 187.6 .8s-14.9 8.9-17.1 17.3L154.7 80.6l-62-17.5c-8.4-2.4-17.4 0-23.5 6.1s-8.5 15.1-6.1 23.5l17.5 62L18.1 170.6c-8.4 2.1-15 8.7-17.3 17.1S1 205 7.3 211l46.2 45L7.3 301C1 307-1.4 316 .8 324.4s8.9 14.9 17.3 17.1l62.5 15.8-17.5 62c-2.4 8.4 0 17.4 6.1 23.5s15.1 8.5 23.5 6.1l62-17.5 15.8 62.5c2.1 8.4 8.7 15 17.1 17.3s17.3-.2 23.4-6.4l45-46.2 45 46.2c6.1 6.2 15 8.7 23.4 6.4s14.9-8.9 17.1-17.3l15.8-62.5 62 17.5c8.4 2.4 17.4 0 23.5-6.1s8.5-15.1 6.1-23.5l-17.5-62 62.5-15.8c8.4-2.1 15-8.7 17.3-17.1s-.2-17.4-6.4-23.4l-46.2-45 46.2-45c6.2-6.1 8.7-15 6.4-23.4s-8.9-14.9-17.3-17.1l-62.5-15.8 17.5-62c2.4-8.4 0-17.4-6.1-23.5s-15.1-8.5-23.5-6.1l-62 17.5L341.4 18.1c-2.1-8.4-8.7-15-17.1-17.3S307 1 301 7.3L256 53.5 211 7.3z"/>' ),
        'check'   => array( '0 0 512 512', '<path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>' ),
        'trophy'  => array( '0 0 576 512', '<path d="M400 0H176c-26.5 0-48.1 21.8-47.1 48.2c.2 5.3 .4 10.6 .7 15.8H24C10.7 64 0 74.7 0 88c0 92.6 33.5 157 78.5 200.7c44.3 43.1 98.3 64.8 138.1 75.8c23.4 6.5 39.4 26 39.4 45.6c0 20.9-17 37.9-37.9 37.9H192c-17.7 0-32 14.3-32 32s14.3 32 32 32H384c17.7 0 32-14.3 32-32s-14.3-32-32-32H357.9C337 448 320 431 320 410.1c0-19.6 15.9-39.2 39.4-45.6c39.9-11 93.9-32.7 138.2-75.8C542.5 245 576 180.6 576 88c0-13.3-10.7-24-24-24H446.4c.3-5.2 .5-10.4 .7-15.8C448.1 21.8 426.5 0 400 0zM48.9 112h84.4c9.1 90.1 29.2 150.3 51.9 190.6c-24.9-11-50.8-26.5-73.2-48.3c-32-31.1-58-76-63-142.3zM464.1 254.3c-22.4 21.8-48.3 37.3-73.2 48.3c22.7-40.3 42.8-100.5 51.9-190.6h84.4c-5.1 66.3-31.1 111.2-63 142.3z"/>' ),
        'pin'     => array( '0 0 384 512', '<path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/>' ),
        'arrow'   => array( '0 0 448 512', '<path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>' ),
        'link'    => array( '0 0 512 512', '<path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32h82.7L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3V192c0 17.7 14.3 32 32 32s32-14.3 32-32V32c0-17.7-14.3-32-32-32H320zM80 32C35.8 32 0 67.8 0 112V432c0 44.2 35.8 80 80 80H400c44.2 0 80-35.8 80-80V320c0-17.7-14.3-32-32-32s-32 14.3-32 32V432c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16H192c17.7 0 32-14.3 32-32s-14.3-32-32-32H80z"/>' ),
        'linkedin'  => array( '0 0 448 512', '<path d="M100.3 448H7.4V148.9h92.9V448zM53.8 108.1C24.1 108.1 0 83.5 0 53.8a53.8 53.8 0 0 1 107.6 0c0 29.7-24.1 54.3-53.8 54.3zM447.9 448h-92.7V302.4c0-34.7-.7-79.2-48.3-79.2-48.3 0-55.7 37.7-55.7 76.7V448h-92.8V148.9h89.1v40.8h1.3c12.4-23.5 42.7-48.3 87.9-48.3 94 0 111.3 61.9 111.3 142.3V448z"/>' ),
        'x'         => array( '0 0 512 512', '<path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/>' ),
        'facebook'  => array( '0 0 320 512', '<path d="M279.1 288l14.2-92.7h-88.9v-60.1c0-25.4 12.4-50.1 52.2-50.1h40.4V6.3S260.4 0 225.4 0c-73.2 0-121.1 44.4-121.1 124.7v70.6H22.9V288h81.4v224h100.2V288z"/>' ),
        'instagram' => array( '0 0 448 512', '<path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/>' ),
        'youtube'   => array( '0 0 576 512', '<path d="M549.7 124.1c-6.3-23.7-24.8-42.3-48.3-48.6C458.8 64 288 64 288 64S117.2 64 74.6 75.5c-23.5 6.3-42 24.9-48.3 48.6-11.4 42.9-11.4 132.3-11.4 132.3s0 89.4 11.4 132.3c6.3 23.7 24.8 41.5 48.3 47.8C117.2 448 288 448 288 448s170.8 0 213.4-11.5c23.5-6.3 42-24.2 48.3-47.8 11.4-42.9 11.4-132.3 11.4-132.3s0-89.4-11.4-132.3zm-317.5 213.5V175.2l142.7 81.2-142.7 81.2z"/>' ),
        'site'      => array( '0 0 512 512', '<path d="M352 256c0 22.2-1.2 43.6-3.3 64H163.3c-2.2-20.4-3.3-41.8-3.3-64s1.2-43.6 3.3-64H348.7c2.2 20.4 3.3 41.8 3.3 64zm28.8-64H503.9c5.3 20.5 8.1 41.9 8.1 64s-2.8 43.5-8.1 64H380.8c2.1-20.6 3.2-42 3.2-64s-1.1-43.4-3.2-64zm112.6-32H376.7c-10-63.9-29.8-117.4-55.3-151.6c78.3 20.7 142 77.5 171.9 151.6zm-149.1 0H167.7c6.1-36.4 15.5-68.6 27-94.7c10.5-23.6 22.2-40.7 33.5-51.5C239.4 3.2 248.7 0 256 0s16.6 3.2 27.8 13.8c11.3 10.8 23 27.9 33.5 51.5c11.6 26 20.9 58.2 27 94.7zm-209 0H18.6C48.6 85.9 112.2 29.1 190.6 8.4C165.1 42.6 145.3 96.1 135.3 160zM8.1 192H131.2c-2.1 20.6-3.2 42-3.2 64s1.1 43.4 3.2 64H8.1C2.8 299.5 0 278.1 0 256s2.8-43.5 8.1-64zM194.7 446.6c-11.6-26-20.9-58.2-27-94.6H344.3c-6.1 36.4-15.5 68.6-27 94.6c-10.5 23.6-22.2 40.7-33.5 51.5C272.6 508.8 263.3 512 256 512s-16.6-3.2-27.8-13.8c-11.3-10.8-23-27.9-33.5-51.5zM135.3 352c10 63.9 29.8 117.4 55.3 151.6C112.2 482.9 48.6 426.1 18.6 352H135.3zm358.1 0c-30 74.1-93.6 130.9-171.9 151.6c25.5-34.2 45.2-87.7 55.3-151.6H493.4z"/>' ),
    );
}

/** SVG لأيقونة مجال أو أيقونة واجهة. */
function yc_author_svg( $key ) {
    $icons = yc_author_icons();
    if ( isset( $icons[ $key ] ) ) {
        return '<svg viewBox="' . $icons[ $key ][1] . '" aria-hidden="true" focusable="false">' . $icons[ $key ][2] . '</svg>';
    }
    $ui = yc_author_ui_icons();
    if ( isset( $ui[ $key ] ) ) {
        return '<svg viewBox="' . $ui[ $key ][0] . '" aria-hidden="true" focusable="false">' . $ui[ $key ][1] . '</svg>';
    }
    return '';
}

/** تخمين أيقونة المجال من عنوانه. */
function yc_author_guess_icon( $title ) {
    $map = array(
        'تنظيف' => 'cleaning', 'نظاف' => 'cleaning',
        'حشر'   => 'pest', 'آفات' => 'pest', 'افات' => 'pest', 'قوارض' => 'pest', 'مكافحة' => 'pest',
        'تسرب'  => 'leak', 'تسريب' => 'leak', 'سباك' => 'leak',
        'عزل'   => 'insulation',
        'صيان'  => 'tools', 'ترميم' => 'tools',
    );
    foreach ( $map as $needle => $key ) {
        if ( false !== mb_strpos( (string) $title, $needle ) ) {
            return $key;
        }
    }
    return 'home';
}

/* =========================================================================
 * القراءة
 * ====================================================================== */

/** قراءة حقل نصي للكاتب. */
function yc_author_get( $user_id, $key ) {
    $v = get_user_meta( $user_id, $key, true );
    return is_scalar( $v ) ? trim( (string) $v ) : '';
}

/** تحويل حقل متعدد الأسطر «أ | ب» إلى صفوف (للبيانات القديمة). */
function yc_author_rows( $raw, $limit = 0 ) {
    $rows = array();
    if ( '' === trim( (string) $raw ) ) {
        return $rows;
    }
    foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
        $line = trim( $line );
        if ( '' === $line ) {
            continue;
        }
        $parts  = preg_split( '/\s*\|\s*/u', $line, 2 );
        $rows[] = array(
            'a' => trim( $parts[0] ),
            'b' => isset( $parts[1] ) ? trim( $parts[1] ) : '',
        );
        if ( $limit && count( $rows ) >= $limit ) {
            break;
        }
    }
    return $rows;
}

/** أسطر نصية غير فارغة. */
function yc_author_lines( $raw ) {
    $out = array();
    foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
        $line = trim( $line );
        if ( '' !== $line ) {
            $out[] = $line;
        }
    }
    return $out;
}

/**
 * قائمة منظمة (مجالات الخبرة / الشهادات / العضويات).
 * تقرأ الصيغة الجديدة (مصفوفة)، وإن لم توجد تحوّل الصيغة القديمة (سطر لكل عنصر).
 */
function yc_author_list( $user_id, $kind ) {
    $keys = array(
        'exp'     => array( 'yc_author_exp_list', 'yc_author_expertise' ),
        'certs'   => array( 'yc_author_cert_list', 'yc_author_certs' ),
        'members' => array( 'yc_author_member_list', 'yc_author_memberships' ),
    );
    if ( ! isset( $keys[ $kind ] ) ) {
        return array();
    }
    $list = get_user_meta( $user_id, $keys[ $kind ][0], true );
    if ( is_array( $list ) ) {
        return array_values( $list );
    }

    $out = array();
    foreach ( yc_author_rows( yc_author_get( $user_id, $keys[ $kind ][1] ) ) as $r ) {
        if ( 'exp' === $kind ) {
            $out[] = array( 'title' => $r['a'], 'cat' => 0, 'icon' => yc_author_guess_icon( $r['a'] ), 'years' => '', 'desc' => $r['b'] );
        } elseif ( 'certs' === $kind ) {
            $out[] = array( 'name' => $r['a'], 'issuer' => $r['b'], 'year' => '', 'url' => '' );
        } else {
            $out[] = array( 'name' => $r['a'], 'type' => $r['b'], 'url' => '' );
        }
    }
    return $out;
}

/** الحسابات المهنية: المفتاح => [meta، التسمية]. */
function yc_author_social_fields() {
    return array(
        'linkedin'  => array( 'yc_author_linkedin', 'LinkedIn' ),
        'x'         => array( 'yc_author_x', 'X (تويتر)' ),
        'facebook'  => array( 'yc_author_facebook', 'Facebook' ),
        'instagram' => array( 'yc_author_instagram', 'Instagram' ),
        'youtube'   => array( 'yc_author_youtube', 'YouTube' ),
        'site'      => array( 'yc_author_site', 'الموقع الشخصي' ),
    );
}

/** كل بيانات الكاتب في مصفوفة واحدة جاهزة للعرض. */
function yc_author_profile( $user_id ) {
    $user = get_userdata( $user_id );
    if ( ! $user ) {
        return array();
    }
    $desc  = trim( wp_strip_all_tags( (string) $user->description ) );
    $long  = trim( wp_strip_all_tags( yc_author_get( $user_id, 'yc_author_long_bio' ) ) );
    $short = trim( wp_strip_all_tags( yc_author_get( $user_id, 'yc_author_short_bio' ) ) );
    if ( '' === $long ) {
        $long = $desc;
    }
    if ( '' === $short ) {
        $short = $long ? wp_trim_words( $long, 45, '…' ) : '';
    }

    $name  = yc_author_get( $user_id, 'yc_author_fullname' );
    $photo = yc_author_get( $user_id, 'yc_author_photo' );

    $socials = array();
    foreach ( yc_author_social_fields() as $k => $f ) {
        $u = yc_author_get( $user_id, $f[0] );
        if ( '' !== $u ) {
            $socials[ $k ] = $u;
        }
    }

    return array(
        'id'           => (int) $user_id,
        'name'         => '' !== $name ? $name : $user->display_name,
        'title'        => yc_author_get( $user_id, 'yc_author_title' ),
        'photo'        => '' !== $photo ? $photo : (string) get_avatar_url( $user_id, array( 'size' => 300 ) ),
        'has_photo'    => '' !== $photo,
        'verified'     => '1' === yc_author_get( $user_id, 'yc_author_verified' ),
        'years'        => yc_author_get( $user_id, 'yc_author_years' ),
        'projects'     => yc_author_get( $user_id, 'yc_author_projects' ),
        'regions'      => yc_author_get( $user_id, 'yc_author_regions' ),
        'short'        => $short,
        'long'         => $long,
        'exp'          => yc_author_list( $user_id, 'exp' ),
        'certs'        => yc_author_list( $user_id, 'certs' ),
        'members'      => yc_author_list( $user_id, 'members' ),
        'achievements' => yc_author_lines( yc_author_get( $user_id, 'yc_author_achievements' ) ),
        'socials'      => $socials,
        'posts'        => (int) count_user_posts( $user_id, 'post' ),
        'url'          => get_author_posts_url( $user_id ),
    );
}

/** رقم بأرقام لاتينية وفواصل آلاف (8,400) — والنص غير الرقمي كما هو. */
function yc_author_num( $v ) {
    $v = trim( (string) $v );
    $d = str_replace( ',', '', $v );
    return ctype_digit( $d ) ? number_format( (int) $d ) : $v;
}

/* =========================================================================
 * نموذج لوحة التحكم
 * ====================================================================== */

/** خيارات الأيقونة. */
function yc_author_icon_options( $selected ) {
    $html = '';
    foreach ( yc_author_icons() as $k => $i ) {
        $html .= '<option value="' . esc_attr( $k ) . '"' . selected( $selected, $k, false ) . '>' . esc_html( $i[0] ) . '</option>';
    }
    return $html;
}

/** خيارات الأقسام. */
function yc_author_cat_options( $selected ) {
    static $terms = null;
    if ( null === $terms ) {
        $terms = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false ) );
        if ( is_wp_error( $terms ) ) {
            $terms = array();
        }
    }
    $html = '<option value="0">— بدون —</option>';
    foreach ( $terms as $t ) {
        $html .= '<option value="' . (int) $t->term_id . '"' . selected( (int) $selected, (int) $t->term_id, false ) . '>' . esc_html( $t->name ) . '</option>';
    }
    return $html;
}

/** صف مجال خبرة. */
function yc_author_exp_row( $i, $r ) {
    $r = wp_parse_args( $r, array( 'title' => '', 'cat' => 0, 'icon' => 'home', 'years' => '', 'desc' => '' ) );
    $n = 'yc_exp[' . $i . ']';
    ob_start();
    ?>
    <div class="yc-rep__row">
        <div class="yc-grid yc-grid--4">
            <p><label>عنوان المجال</label><input type="text" name="<?php echo esc_attr( $n ); ?>[title]" value="<?php echo esc_attr( $r['title'] ); ?>" class="yc-exp-title" placeholder="مثل: إدارة المكافحة المتكاملة للآفات"></p>
            <p><label>القسم المرتبط</label><select name="<?php echo esc_attr( $n ); ?>[cat]" class="yc-exp-cat"><?php echo yc_author_cat_options( $r['cat'] ); // phpcs:ignore ?></select></p>
            <p><label>الأيقونة</label><select name="<?php echo esc_attr( $n ); ?>[icon]" class="yc-exp-icon"><?php echo yc_author_icon_options( $r['icon'] ); // phpcs:ignore ?></select></p>
            <p><label>سنوات الخبرة في المجال</label><input type="number" min="0" max="80" name="<?php echo esc_attr( $n ); ?>[years]" value="<?php echo esc_attr( $r['years'] ); ?>"></p>
        </div>
        <p><label>وصف الخبرة الميدانية</label><textarea rows="3" name="<?php echo esc_attr( $n ); ?>[desc]" placeholder="ما الذي نفّذه الكاتب فعليًا في هذا المجال: الأساليب، المواد، المعايير، نوع المشاريع."><?php echo esc_textarea( $r['desc'] ); ?></textarea></p>
        <a href="#" class="yc-rep__del">حذف هذا المجال</a>
    </div>
    <?php
    return ob_get_clean();
}

/** صف شهادة. */
function yc_author_cert_row( $i, $r ) {
    $r = wp_parse_args( $r, array( 'name' => '', 'issuer' => '', 'year' => '', 'url' => '' ) );
    $n = 'yc_cert[' . $i . ']';
    ob_start();
    ?>
    <div class="yc-rep__row">
        <div class="yc-grid yc-grid--4">
            <p><label>اسم الشهادة</label><input type="text" name="<?php echo esc_attr( $n ); ?>[name]" value="<?php echo esc_attr( $r['name'] ); ?>"></p>
            <p><label>الجهة المانحة</label><input type="text" name="<?php echo esc_attr( $n ); ?>[issuer]" value="<?php echo esc_attr( $r['issuer'] ); ?>"></p>
            <p><label>سنة الحصول</label><input type="number" min="1950" max="2100" name="<?php echo esc_attr( $n ); ?>[year]" value="<?php echo esc_attr( $r['year'] ); ?>"></p>
            <p><label>رابط التحقق (اختياري)</label><input type="url" dir="ltr" name="<?php echo esc_attr( $n ); ?>[url]" value="<?php echo esc_attr( $r['url'] ); ?>" placeholder="https://"></p>
        </div>
        <a href="#" class="yc-rep__del">حذف</a>
    </div>
    <?php
    return ob_get_clean();
}

/** صف عضوية. */
function yc_author_member_row( $i, $r ) {
    $r = wp_parse_args( $r, array( 'name' => '', 'type' => '', 'url' => '' ) );
    $n = 'yc_member[' . $i . ']';
    ob_start();
    ?>
    <div class="yc-rep__row">
        <div class="yc-grid yc-grid--3">
            <p><label>الجمعية أو الهيئة</label><input type="text" name="<?php echo esc_attr( $n ); ?>[name]" value="<?php echo esc_attr( $r['name'] ); ?>"></p>
            <p><label>نوع العضوية</label><input type="text" name="<?php echo esc_attr( $n ); ?>[type]" value="<?php echo esc_attr( $r['type'] ); ?>" placeholder="مثل: عضو مهني ممارس"></p>
            <p><label>رابط (اختياري)</label><input type="url" dir="ltr" name="<?php echo esc_attr( $n ); ?>[url]" value="<?php echo esc_attr( $r['url'] ); ?>" placeholder="https://"></p>
        </div>
        <a href="#" class="yc-rep__del">حذف</a>
    </div>
    <?php
    return ob_get_clean();
}

/** صفحة الملف الشخصي. */
function yc_author_profile_fields( $user ) {
    if ( ! is_object( $user ) ) {
        return;
    }
    $uid = $user->ID;
    $v   = function ( $key ) use ( $uid ) {
        return yc_author_get( $uid, $key );
    };
    $exp     = yc_author_list( $uid, 'exp' );
    $certs   = yc_author_list( $uid, 'certs' );
    $members = yc_author_list( $uid, 'members' );
    $photo   = $v( 'yc_author_photo' );
    ?>
    <div class="yc-eeat" id="yc-eeat">
        <?php wp_nonce_field( 'yc_author_save', 'yc_author_nonce' ); ?>
        <h2>ملف الخبير (معايير E-E-A-T)</h2>
        <p class="yc-eeat__intro">هذه البيانات تظهر في صفحة الكاتب وفي صندوق «نبذة عن الكاتب» أسفل كل مقال، وتُضاف إلى البيانات المنظّمة التي تقرأها محركات البحث. اكتب معلومات حقيقية فقط — الشهادات والعضويات غير الحقيقية تضر الموقع أكثر مما تنفعه.</p>

        <!-- 1) التعريف الأساسي -->
        <div class="yc-eeat__sec">
            <h3><span>1</span> التعريف الأساسي</h3>
            <div class="yc-grid yc-grid--2">
                <p><label for="yc_author_fullname">الاسم الكامل (ثنائي أو ثلاثي)</label><input type="text" id="yc_author_fullname" name="yc_author_fullname" value="<?php echo esc_attr( $v( 'yc_author_fullname' ) ); ?>" placeholder="<?php echo esc_attr( $user->display_name ); ?>"></p>
                <p><label for="yc_author_title">المسمى الوظيفي الدقيق</label><input type="text" id="yc_author_title" name="yc_author_title" value="<?php echo esc_attr( $v( 'yc_author_title' ) ); ?>" placeholder="مثل: مدير قطاع مكافحة الحشرات"></p>
            </div>
            <div class="yc-photo">
                <div class="yc-photo__img" id="yc-photo-preview"><?php echo $photo ? '<img src="' . esc_url( $photo ) . '" alt="">' : get_avatar( $uid, 90 ); ?></div>
                <div class="yc-photo__body">
                    <input type="hidden" id="yc_author_photo" name="yc_author_photo" value="<?php echo esc_attr( $photo ); ?>">
                    <button type="button" class="button button-primary" id="yc-photo-pick">اختيار صورة شخصية</button>
                    <button type="button" class="button-link yc-photo__remove" id="yc-photo-remove"<?php echo $photo ? '' : ' hidden'; ?>>إزالة</button>
                    <p class="description">صورة حقيقية وواضحة — يُفضّل بملابس العمل الميداني. مربعة بمقاس 600×600 على الأقل.</p>
                </div>
            </div>
            <label class="yc-check"><input type="checkbox" name="yc_author_verified" value="1" <?php checked( $v( 'yc_author_verified' ), '1' ); ?>> إظهار شارة «خبير موثّق» (فعّلها فقط بعد التحقق من هوية الكاتب وشهاداته)</label>
        </div>

        <!-- 2) الخبرة والأرقام -->
        <div class="yc-eeat__sec">
            <h3><span>2</span> الخبرة والأرقام</h3>
            <div class="yc-grid yc-grid--3">
                <p><label for="yc_author_years">إجمالي سنوات الخبرة الميدانية</label><input type="number" min="0" max="80" id="yc_author_years" name="yc_author_years" value="<?php echo esc_attr( $v( 'yc_author_years' ) ); ?>"></p>
                <p><label for="yc_author_projects">عدد المعاينات أو المشاريع المنفذة</label><input type="number" min="0" id="yc_author_projects" name="yc_author_projects" value="<?php echo esc_attr( str_replace( ',', '', $v( 'yc_author_projects' ) ) ); ?>"></p>
                <p><label for="yc_author_regions">مناطق العمل</label><input type="text" id="yc_author_regions" name="yc_author_regions" value="<?php echo esc_attr( $v( 'yc_author_regions' ) ); ?>" placeholder="مثل: المنطقة الشرقية"></p>
            </div>
            <p><label for="yc_author_short_bio">نبذة مختصرة (تظهر أسفل المقالات — سطران أو ثلاثة)</label><textarea id="yc_author_short_bio" name="yc_author_short_bio" rows="3"><?php echo esc_textarea( $v( 'yc_author_short_bio' ) ); ?></textarea></p>
            <p><label for="yc_author_long_bio">نبذة تفصيلية (تظهر في صفحة الكاتب — اتركها فارغة لاستخدام «معلومات السيرة الذاتية» أعلاه)</label><textarea id="yc_author_long_bio" name="yc_author_long_bio" rows="6"><?php echo esc_textarea( $v( 'yc_author_long_bio' ) ); ?></textarea></p>
        </div>

        <!-- 3) الخبرة حسب القسم -->
        <div class="yc-eeat__sec">
            <h3><span>3</span> الخبرة الميدانية حسب القسم</h3>
            <p class="description">مجال لكل قسم يكتب فيه الكاتب. اربط كل مجال بقسمه حتى يظهر رابط «مقالاته في هذا القسم».</p>
            <div class="yc-rep" id="yc-rep-exp" data-next="<?php echo count( $exp ); ?>">
                <?php foreach ( $exp as $i => $r ) { echo yc_author_exp_row( $i, $r ); } // phpcs:ignore ?>
            </div>
            <p class="yc-rep__actions">
                <button type="button" class="button yc-rep__add" data-rep="exp">+ إضافة مجال خبرة</button>
                <button type="button" class="button" id="yc-exp-suggest">تعبئة المجالات الأربعة المقترحة</button>
            </p>
        </div>

        <!-- 4) الشهادات -->
        <div class="yc-eeat__sec">
            <h3><span>4</span> الشهادات والاعتمادات الرسمية</h3>
            <p class="description">أهم جزء في تقييم جوجل لمواقع الخدمات (YMYL): رخص البلدية، شهادات السلامة المهنية، والشهادات الهندسية.</p>
            <div class="yc-rep" id="yc-rep-cert" data-next="<?php echo count( $certs ); ?>">
                <?php foreach ( $certs as $i => $r ) { echo yc_author_cert_row( $i, $r ); } // phpcs:ignore ?>
            </div>
            <p class="yc-rep__actions"><button type="button" class="button yc-rep__add" data-rep="cert">+ إضافة شهادة</button></p>
        </div>

        <!-- 5) العضويات -->
        <div class="yc-eeat__sec">
            <h3><span>5</span> العضويات المهنية</h3>
            <p class="description">الانتساب لجمعيات أو هيئات محلية أو دولية ذات صلة.</p>
            <div class="yc-rep" id="yc-rep-member" data-next="<?php echo count( $members ); ?>">
                <?php foreach ( $members as $i => $r ) { echo yc_author_member_row( $i, $r ); } // phpcs:ignore ?>
            </div>
            <p class="yc-rep__actions"><button type="button" class="button yc-rep__add" data-rep="member">+ إضافة عضوية</button></p>
        </div>

        <!-- 6) الإنجازات -->
        <div class="yc-eeat__sec">
            <h3><span>6</span> سجل الإنجازات</h3>
            <p><label for="yc_author_achievements">إنجاز في كل سطر</label><textarea id="yc_author_achievements" name="yc_author_achievements" rows="5"><?php echo esc_textarea( $v( 'yc_author_achievements' ) ); ?></textarea></p>
        </div>

        <!-- 7) الحسابات -->
        <div class="yc-eeat__sec">
            <h3><span>7</span> الحسابات المهنية الموثّقة</h3>
            <div class="yc-grid yc-grid--3">
                <?php foreach ( yc_author_social_fields() as $k => $f ) : ?>
                    <p><label for="<?php echo esc_attr( $f[0] ); ?>"><?php echo esc_html( $f[1] ); ?></label><input type="url" dir="ltr" id="<?php echo esc_attr( $f[0] ); ?>" name="<?php echo esc_attr( $f[0] ); ?>" value="<?php echo esc_attr( $v( $f[0] ) ); ?>" placeholder="https://"></p>
                <?php endforeach; ?>
            </div>
        </div>

        <script type="text/template" id="yc-tpl-exp"><?php echo yc_author_exp_row( '__i__', array() ); // phpcs:ignore ?></script>
        <script type="text/template" id="yc-tpl-cert"><?php echo yc_author_cert_row( '__i__', array() ); // phpcs:ignore ?></script>
        <script type="text/template" id="yc-tpl-member"><?php echo yc_author_member_row( '__i__', array() ); // phpcs:ignore ?></script>
    </div>
    <?php
}
add_action( 'show_user_profile', 'yc_author_profile_fields' );
add_action( 'edit_user_profile', 'yc_author_profile_fields' );

/* =========================================================================
 * الحفظ
 * ====================================================================== */
function yc_author_profile_save( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return;
    }
    if ( ! isset( $_POST['yc_author_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['yc_author_nonce'] ) ), 'yc_author_save' ) ) {
        return;
    }
    $post = wp_unslash( $_POST );

    // نصوص قصيرة
    foreach ( array( 'yc_author_fullname', 'yc_author_title', 'yc_author_regions' ) as $k ) {
        update_user_meta( $user_id, $k, isset( $post[ $k ] ) ? sanitize_text_field( $post[ $k ] ) : '' );
    }
    // أرقام
    foreach ( array( 'yc_author_years', 'yc_author_projects' ) as $k ) {
        $n = isset( $post[ $k ] ) ? preg_replace( '/\D/', '', (string) $post[ $k ] ) : '';
        update_user_meta( $user_id, $k, $n );
    }
    // نصوص طويلة
    foreach ( array( 'yc_author_short_bio', 'yc_author_long_bio', 'yc_author_achievements' ) as $k ) {
        update_user_meta( $user_id, $k, isset( $post[ $k ] ) ? sanitize_textarea_field( $post[ $k ] ) : '' );
    }
    // روابط
    update_user_meta( $user_id, 'yc_author_photo', isset( $post['yc_author_photo'] ) ? esc_url_raw( $post['yc_author_photo'] ) : '' );
    foreach ( yc_author_social_fields() as $f ) {
        update_user_meta( $user_id, $f[0], isset( $post[ $f[0] ] ) ? esc_url_raw( $post[ $f[0] ] ) : '' );
    }
    update_user_meta( $user_id, 'yc_author_verified', empty( $post['yc_author_verified'] ) ? '' : '1' );

    // مجالات الخبرة
    $icons = yc_author_icons();
    $exp   = array();
    foreach ( ( isset( $post['yc_exp'] ) && is_array( $post['yc_exp'] ) ? $post['yc_exp'] : array() ) as $r ) {
        $title = isset( $r['title'] ) ? sanitize_text_field( $r['title'] ) : '';
        if ( '' === $title ) {
            continue;
        }
        $icon  = isset( $r['icon'] ) ? sanitize_key( $r['icon'] ) : '';
        $exp[] = array(
            'title' => $title,
            'cat'   => isset( $r['cat'] ) ? absint( $r['cat'] ) : 0,
            'icon'  => isset( $icons[ $icon ] ) ? $icon : yc_author_guess_icon( $title ),
            'years' => isset( $r['years'] ) ? preg_replace( '/\D/', '', (string) $r['years'] ) : '',
            'desc'  => isset( $r['desc'] ) ? sanitize_textarea_field( $r['desc'] ) : '',
        );
    }
    update_user_meta( $user_id, 'yc_author_exp_list', $exp );

    // الشهادات
    $certs = array();
    foreach ( ( isset( $post['yc_cert'] ) && is_array( $post['yc_cert'] ) ? $post['yc_cert'] : array() ) as $r ) {
        $name = isset( $r['name'] ) ? sanitize_text_field( $r['name'] ) : '';
        if ( '' === $name ) {
            continue;
        }
        $certs[] = array(
            'name'   => $name,
            'issuer' => isset( $r['issuer'] ) ? sanitize_text_field( $r['issuer'] ) : '',
            'year'   => isset( $r['year'] ) ? substr( preg_replace( '/\D/', '', (string) $r['year'] ), 0, 4 ) : '',
            'url'    => isset( $r['url'] ) ? esc_url_raw( $r['url'] ) : '',
        );
    }
    update_user_meta( $user_id, 'yc_author_cert_list', $certs );

    // العضويات
    $members = array();
    foreach ( ( isset( $post['yc_member'] ) && is_array( $post['yc_member'] ) ? $post['yc_member'] : array() ) as $r ) {
        $name = isset( $r['name'] ) ? sanitize_text_field( $r['name'] ) : '';
        if ( '' === $name ) {
            continue;
        }
        $members[] = array(
            'name' => $name,
            'type' => isset( $r['type'] ) ? sanitize_text_field( $r['type'] ) : '',
            'url'  => isset( $r['url'] ) ? esc_url_raw( $r['url'] ) : '',
        );
    }
    update_user_meta( $user_id, 'yc_author_member_list', $members );

    // الصيغة القديمة لم تعد تُستخدم بعد أول حفظ
    foreach ( array( 'yc_author_expertise', 'yc_author_certs', 'yc_author_memberships', 'yc_author_stats' ) as $old ) {
        delete_user_meta( $user_id, $old );
    }
}
add_action( 'personal_options_update', 'yc_author_profile_save' );
add_action( 'edit_user_profile_update', 'yc_author_profile_save' );

/* =========================================================================
 * أنماط وسكربت النموذج (صفحتا الملف الشخصي فقط)
 * ====================================================================== */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( ! in_array( $hook, array( 'profile.php', 'user-edit.php' ), true ) ) {
        return;
    }
    wp_enqueue_media();
    add_action( 'admin_footer', 'yc_author_admin_assets' );
} );

function yc_author_admin_assets() {
    ?>
<style>
.yc-eeat{background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:22px 26px;margin:28px 0;max-width:1100px}
.yc-eeat>h2{margin:0 0 6px;font-size:19px}
.yc-eeat__intro{color:#50575e;margin:0 0 6px;max-width:820px;line-height:1.8}
.yc-eeat__sec{border-top:1px solid #f0f0f1;padding:18px 0 6px}
.yc-eeat__sec h3{display:flex;align-items:center;gap:8px;margin:0 0 12px;font-size:15px}
.yc-eeat__sec h3 span{display:inline-grid;place-items:center;width:24px;height:24px;border-radius:7px;background:#2764c3;color:#fff;font-size:12px}
.yc-eeat label{display:block;font-weight:600;margin-bottom:5px;color:#1d2327}
.yc-eeat p{margin:0 0 12px}
.yc-eeat input[type=text],.yc-eeat input[type=url],.yc-eeat input[type=number],.yc-eeat select,.yc-eeat textarea{width:100%;max-width:none}
.yc-eeat textarea{line-height:1.8}
.yc-grid{display:grid;gap:0 14px}
.yc-grid--2{grid-template-columns:repeat(2,1fr)}
.yc-grid--3{grid-template-columns:repeat(3,1fr)}
.yc-grid--4{grid-template-columns:repeat(4,1fr)}
.yc-photo{display:flex;align-items:center;gap:16px;margin:4px 0 14px}
.yc-photo__img{width:90px;height:90px;border-radius:14px;overflow:hidden;background:#f0f0f1;flex-shrink:0}
.yc-photo__img img{width:100%;height:100%;object-fit:cover;display:block}
.yc-photo__remove{color:#b32d2e!important;margin-inline-start:8px!important}
.yc-photo .description{margin-top:8px}
.yc-check{font-weight:600}
.yc-check input{margin-inline-end:6px}
.yc-rep__row{background:#f6f7f7;border:1px solid #e0e0e0;border-radius:10px;padding:14px 16px 10px;margin-bottom:12px}
.yc-rep__del{color:#b32d2e;font-size:12.5px;text-decoration:none}
.yc-rep__del:hover{color:#8a2424;text-decoration:underline}
.yc-rep__actions{display:flex;gap:8px;flex-wrap:wrap}
@media (max-width:900px){.yc-grid--3,.yc-grid--4{grid-template-columns:repeat(2,1fr)}}
@media (max-width:600px){.yc-grid--2,.yc-grid--3,.yc-grid--4{grid-template-columns:1fr}.yc-eeat{padding:16px}}
</style>
<script>
jQuery(function ($) {
  var $box = $('#yc-eeat');
  if (!$box.length) { return; }

  function addRow(kind, data) {
    var $rep = $('#yc-rep-' + kind);
    var i = parseInt($rep.attr('data-next'), 10) || 0;
    $rep.attr('data-next', i + 1);
    var $row = $($('#yc-tpl-' + kind).html().replace(/__i__/g, i));
    if (data) {
      $.each(data, function (k, v) { $row.find('[name$="[' + k + ']"]').val(v); });
    }
    $rep.append($row);
    return $row;
  }

  $box.on('click', '.yc-rep__add', function (e) {
    e.preventDefault();
    addRow($(this).data('rep')).find('input:first').trigger('focus');
  });

  $box.on('click', '.yc-rep__del', function (e) {
    e.preventDefault();
    if (window.confirm('حذف هذا العنصر؟')) { $(this).closest('.yc-rep__row').remove(); }
  });

  // المجالات الأربعة المقترحة — يُربط كل مجال بالقسم الذي يطابق اسمه إن وُجد
  $('#yc-exp-suggest').on('click', function (e) {
    e.preventDefault();
    var list = [
      { title: 'خدمات التنظيف', icon: 'cleaning', key: ['تنظيف'] },
      { title: 'مكافحة الحشرات والقوارض', icon: 'pest', key: ['حشر', 'مكافحة'] },
      { title: 'كشف تسربات المياه', icon: 'leak', key: ['تسرب', 'تسريب'] },
      { title: 'العزل المائي والحراري', icon: 'insulation', key: ['عزل'] }
    ];
    // يُتخطّى المجال الموجود مسبقًا (بنفس الأيقونة أو نفس العنوان)
    var have = $('#yc-rep-exp .yc-rep__row').map(function () {
      return [$.trim($(this).find('.yc-exp-title').val()), $(this).find('.yc-exp-icon').val()];
    }).get();
    $.each(list, function (_, s) {
      if ($.inArray(s.title, have) !== -1 || $.inArray(s.icon, have) !== -1) { return; }
      var $row = addRow('exp', { title: s.title, icon: s.icon });
      $row.find('.yc-exp-cat option').each(function () {
        var t = $(this).text();
        for (var k = 0; k < s.key.length; k++) {
          if (t.indexOf(s.key[k]) !== -1) { $row.find('.yc-exp-cat').val(this.value); return false; }
        }
      });
    });
  });

  // الصورة
  var frame;
  $('#yc-photo-pick').on('click', function (e) {
    e.preventDefault();
    if (typeof wp === 'undefined' || !wp.media) { return; }
    if (!frame) {
      frame = wp.media({ title: 'اختر صورة الكاتب', multiple: false, library: { type: 'image' } });
      frame.on('select', function () {
        var url = frame.state().get('selection').first().toJSON().url;
        $('#yc_author_photo').val(url);
        $('#yc-photo-preview').html($('<img alt="">').attr('src', url));
        $('#yc-photo-remove').prop('hidden', false);
      });
    }
    frame.open();
  });
  $('#yc-photo-remove').on('click', function (e) {
    e.preventDefault();
    $('#yc_author_photo').val('');
    $('#yc-photo-preview').empty();
    $(this).prop('hidden', true);
  });
});
</script>
    <?php
}
