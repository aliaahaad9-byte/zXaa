<?php echo '<!DOCTYPE html>';
echo '<html lang="ar" dir="rtl">';
echo '<head>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
echo '<meta charset="utf-8">';
//
/* لا يُطبع <title> يدويًا هنا: ووردبريس يطبعه مرة واحدة داخل wp_head()
   عبر دعم title-tag، فتتولاه إضافة السيو بعنوانها المحسّن بلا تكرار. */
do_action('BeforeWPHead');
 wp_head(); 

do_action('AfterWPHead');
if (strpos(($_SERVER['HTTP_USER_AGENT'] ?? ''), 'Lighthouse') === false ) {
    if(!empty(get_option('favicon')['url'])){
        echo '<link rel="shortcut icon" type="image/png" href="'.get_option('favicon')['url'].'">';
    }
}
/* وسم الوصف: يُطبع فقط إن لم تكن هناك إضافة سيو تطبع وصفها الخاص،
   منعًا لتكرار meta description في نفس الصفحة. */
$seo__plugin_active = (
    defined('RANK_MATH_VERSION') || defined('WPSEO_VERSION') ||
    defined('SEOPRESS_VERSION')  || defined('AIOSEO_VERSION') ||
    class_exists('RankMath')     || class_exists('WPSEO_Frontend')
);
$disable__theme_description = get_option('disable_description');
if( empty( $disable__theme_description ) && !$seo__plugin_active ){
    echo'<meta name="description" content="'.esc_attr(get_bloginfo("name")).'">';
}


if(IsSpeed() == false){
    $fontawesomePath = $this->StylesPath."fontawesome/css/*.css";
$fontawesomeCss = glob($fontawesomePath);

$fontawesomeURL = $this->StylesURL.'fontawesome/css/';

echo '<link rel="stylesheet" href="'.$fontawesomeURL.'fontawesome.css">';
foreach ( $fontawesomeCss as $file ) {
    // fontawesome.css مُحمَّل بالأعلى، فلا يُكرَّر هنا
    if ( basename($file) === 'fontawesome.css' ) { continue; }
    echo '<link rel="stylesheet" href="'.$fontawesomeURL.basename($file).'">';
}
}

echo '<meta name="apple-mobile-web-app-title" content="'.get_bloginfo("name").'">';
echo '<meta http-equiv="Cache-control" content="public">';
echo '<meta name="application-name" content="'.get_bloginfo("name").'">';
echo '<meta name="msapplication-TileColor" content="#a03576">';
//
 //echo '<link rel="stylesheet" media="all" href="'.$this->StylesURL.'main.css?'.rand().'" />';
 //echo '<link rel="stylesheet" media="all" href="'.$this->StylesURL.'responsive.css?'.rand().'" />';
 	echo '<style>';
 	 	if (strpos(($_SERVER['HTTP_USER_AGENT'] ?? ''), 'Lighthouse') === false ) {
			echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 100;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaijdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0600-06FF, U+0750-077F, U+0870-088E, U+0890-0891, U+0898-08E1, U+08E3-08FF, U+200C-200E, U+2010-2011, U+204F, U+2E41, U+FB50-FDFF, U+FE70-FE74, U+FE76-FEFC;';
		echo '}	';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 100;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiLdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;';
		echo '}	';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 100;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiPdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;';
		echo '}	';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 100;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeai3dAw.woff2) format("woff2");';
		  echo 'unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;';
		echo '}	';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 200;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaijdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0600-06FF, U+0750-077F, U+0870-088E, U+0890-0891, U+0898-08E1, U+08E3-08FF, U+200C-200E, U+2010-2011, U+204F, U+2E41, U+FB50-FDFF, U+FE70-FE74, U+FE76-FEFC;';
		echo '}	';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 200;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiLdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;';
		echo '}	';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 200;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiPdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;';
		echo '}	';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 200;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeai3dAw.woff2) format("woff2");';
		  echo 'unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 300;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaijdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0600-06FF, U+0750-077F, U+0870-088E, U+0890-0891, U+0898-08E1, U+08E3-08FF, U+200C-200E, U+2010-2011, U+204F, U+2E41, U+FB50-FDFF, U+FE70-FE74, U+FE76-FEFC;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 300;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiLdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 300;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiPdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 300;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeai3dAw.woff2) format("woff2");';
		  echo 'unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 400;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaijdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0600-06FF, U+0750-077F, U+0870-088E, U+0890-0891, U+0898-08E1, U+08E3-08FF, U+200C-200E, U+2010-2011, U+204F, U+2E41, U+FB50-FDFF, U+FE70-FE74, U+FE76-FEFC;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 400;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiLdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 400;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiPdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 400;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeai3dAw.woff2) format("woff2");';
		  echo 'unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 500;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaijdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0600-06FF, U+0750-077F, U+0870-088E, U+0890-0891, U+0898-08E1, U+08E3-08FF, U+200C-200E, U+2010-2011, U+204F, U+2E41, U+FB50-FDFF, U+FE70-FE74, U+FE76-FEFC;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 500;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiLdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 500;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiPdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 500;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeai3dAw.woff2) format("woff2");';
		  echo 'unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 600;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaijdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0600-06FF, U+0750-077F, U+0870-088E, U+0890-0891, U+0898-08E1, U+08E3-08FF, U+200C-200E, U+2010-2011, U+204F, U+2E41, U+FB50-FDFF, U+FE70-FE74, U+FE76-FEFC;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 600;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiLdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 600;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiPdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 600;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeai3dAw.woff2) format("woff2");';
		  echo 'unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 700;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaijdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0600-06FF, U+0750-077F, U+0870-088E, U+0890-0891, U+0898-08E1, U+08E3-08FF, U+200C-200E, U+2010-2011, U+204F, U+2E41, U+FB50-FDFF, U+FE70-FE74, U+FE76-FEFC;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 700;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiLdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 700;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiPdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 700;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeai3dAw.woff2) format("woff2");';
		  echo 'unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 800;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaijdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0600-06FF, U+0750-077F, U+0870-088E, U+0890-0891, U+0898-08E1, U+08E3-08FF, U+200C-200E, U+2010-2011, U+204F, U+2E41, U+FB50-FDFF, U+FE70-FE74, U+FE76-FEFC;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 800;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiLdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 800;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiPdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 800;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeai3dAw.woff2) format("woff2");';
		  echo 'unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 900;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaijdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0600-06FF, U+0750-077F, U+0870-088E, U+0890-0891, U+0898-08E1, U+08E3-08FF, U+200C-200E, U+2010-2011, U+204F, U+2E41, U+FB50-FDFF, U+FE70-FE74, U+FE76-FEFC;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 900;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiLdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 900;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeaiPdA4M5.woff2) format("woff2");';
		  echo 'unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;';
		echo '}';
		echo '@font-face {';
		  echo 'font-family: "Alexandria";';
		  echo 'font-style: normal;';
		  echo 'font-weight: 900;';
		  echo 'font-display: swap;';
		  echo 'src: url('.$this->StylesURL.'Font/Alexandria/UMBXrPdDqW66y0Y2usFeai3dAw.woff2) format("woff2");';
		  echo 'unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;';
		echo '}';
		
		}
		require get_template_directory().'/components/styles/main.css';
    	require get_template_directory().'/components/styles/responsive.css';
	echo '</style>';
	if( IsSpeed() == false){
			
			$show_headCodes = wp_is_mobile() ? get_option('show_ads_mobile_header') : get_option('show_ads_header');
			$ads_code = wp_is_mobile() ? get_option('mobile_ads_header', '') : get_option('ads_header', '');

			if (wp_is_mobile() && empty($show_headCodes)) {
			    echo get_option('mobile_ads_header', '');
			} else if (!wp_is_mobile() && empty($show_headCodes)) {
			    echo get_option('ads_header', '');
			}

			
		}
echo '</head>';
echo '<body mode="light">';
echo '<root>';
echo '<rootinse>';
$logo = get_option('logo');


$Whatsapp = get_option('Whatsapp');
$Phone = get_option('Phone');
if( is_single() ) {
	wp_reset_query();
	global $post;
	$call_number = get_post_meta($post->ID, 'call_number', true);
	$whatsapp_number = get_post_meta($post->ID, 'whatsapp_number', true);
	if( !empty($call_number) ) {
		$Phone = $call_number;
	}
	if( !empty($whatsapp_number) ) {
		$Whatsapp = $whatsapp_number;
	}
}elseif( is_category() ) {
	$current_object = get_queried_object();
	$call_number = get_term_meta($current_object->term_id, 'call_number', true);
	$whatsapp_number = get_term_meta($current_object->term_id, 'whatsapp_number', true);
	if( !empty($call_number) ) {
		$Phone = $call_number;
	}
	if( !empty($whatsapp_number) ) {
		$Whatsapp = $whatsapp_number;
	}
}
elseif( is_tax('country') ) {
	$current_object = get_queried_object();
	$call_number = get_term_meta($current_object->term_id, 'call_number', true);
	$whatsapp_number = get_term_meta($current_object->term_id, 'whatsapp_number', true);
	if( !empty($call_number) ) {
		$Phone = $call_number;
	}
	if( !empty($whatsapp_number) ) {
		$Whatsapp = $whatsapp_number;
	}
}

echo'<header>';
	echo'<div class="container">';
			
		echo'<div class="logo">';
		    if(IsSpeed() == false){
		        
		    
			if( !empty( $logo ) && isset( $logo['url'] ) && !empty( $logo['url'] ) ){
				$wp_get_attachment_metadata = wp_get_attachment_metadata( $logo['id'] );		
				echo'<a href="'.home_url().'"><img width=100% height=100%  alt="'.get_bloginfo('name').'" src="'.$logo['url'].'"></a>';
			}
		    }
		echo'</div>';
		echo'<div class="menu-nav">';
		    if(IsSpeed() == false){
			echo'<form method="GET" action="'.home_url().'" style="display:none;">';

		        echo'<input type="seach" name="s" placeholder="أدخل كلمة البحث" />';

		        echo'<button  aria-label="seach" type="submit"><i class="fa fa-search"></i></button>';

		    echo'</form>'; 
		    }
			wp_nav_menu(
		       array(
	               'theme_location' => 'home-menu',
	               'menu'           => '',
	               'container'      => '',
	               'container_class' => '',
	               'container_id'   => '',
	               'menu_class'     => '',
	               'menu_id'        => '',
	               'echo'           => true,
	               'fallback_cb'    => 'wp_page_menu',
	               'before'         => '',
	               'after'          => '',
	               'link_before'    => '',
	               'link_after'     => '',
	               'items_wrap'     => '<ul class ="header-menu">%3$s</ul>',
	               'depth'          => 0,
	               'walker'         => '',
				)
			);
			
		echo'</div>';
		echo '<div class="menu-barbox">';
			echo '<div class ="menu_bar">';
	    		echo'<i class="fa-solid fa-bars"></i>';
	    		echo '<i class="fa-thin fa-xmark"></i>';
	    	echo'</div>';
	    	echo '<div class ="search_header">';
	    		echo '<span id="openSEarch"><i class="far fa-search"></i><i class="fa-solid fa-xmark"></i></span>';
		    	echo '<form action="'.home_url().'" method="GET" >';
		            echo '<input type="text" name="s" placeholder="إبحث " >';
		            echo '<button aria-label="name" title="Search" type="submit">بحث</button>';
		        echo '</form>';
	        echo '</div>';
        echo '</div>';
	echo '</div>';
echo'</header>';
