<?php
/**
 * نموذج الصفحة: العروض.
 * البيانات من «الحاسبة والعروض ← العروض» في لوحة التحكم.
 */
if ( ! function_exists( 'yc_offers_data' ) ) {
    return;
}
global $post;
$d     = yc_offers_data();
$cur   = $d['currency'];
$wa    = yc_pt_wa_number( $d['wa'] );
$phone = yc_pt_phone();
$today = current_time( 'Y-m-d' );

$offers = array();
foreach ( $d['offers'] as $o ) {
    if ( $d['hide_expired'] && ! empty( $o['expires'] ) && $o['expires'] < $today ) {
        continue;
    }
    $offers[] = $o;
}

$css = get_template_directory() . '/components/packs/PricingTools/tools.css';
if ( file_exists( $css ) ) {
    echo '<style>' . file_get_contents( $css ) . '</style>';
}

echo '<section class="yc-pt-page yc-offers">';
echo '<div class="container">';

    echo '<div class="yc-pt-crumb"><breadcrumb>';
        Breadcrumb();
    echo '</breadcrumb></div>';

    echo '<div class="yc-pt-head">';
        echo '<h1>' . esc_html( get_the_title( $post ) ) . '</h1>';
        $content = trim( (string) $post->post_content );
        if ( '' !== $content ) {
            echo '<div class="yc-pt-intro">' . apply_filters( 'the_content', $content ) . '</div>';
        }
    echo '</div>';

    if ( ! $offers ) {
        echo '<p class="yc-pt-empty">لا توجد عروض متاحة حاليًا — تابعنا قريبًا.</p>';
    } else {
        echo '<div class="yc-offers__grid">';
        foreach ( $offers as $o ) {
            $old  = ( '' !== $o['old'] && (float) $o['old'] > (float) $o['new'] ) ? (float) $o['old'] : 0;
            $new  = (float) $o['new'];
            $save = $old ? (int) round( ( $old - $new ) / $old * 100 ) : 0;

            $days = null;
            if ( ! empty( $o['expires'] ) ) {
                $days = (int) floor( ( strtotime( $o['expires'] . ' 23:59:59' ) - strtotime( current_time( 'mysql' ) ) ) / DAY_IN_SECONDS );
            }

            echo '<article class="yc-offer' . ( $o['featured'] ? ' is-featured' : '' ) . '">';

                echo '<div class="yc-offer__media">';
                    if ( ! empty( $o['image'] ) ) {
                        echo '<img src="' . esc_url( $o['image'] ) . '" alt="' . esc_attr( $o['title'] ) . '" loading="lazy" width="400" height="220">';
                    } else {
                        echo '<span class="yc-offer__icon">' . yc_pt_icon( $o['icon'] ) . '</span>';
                    }
                    if ( $save > 0 ) {
                        echo '<span class="yc-offer__save">وفّر ' . esc_html( $save ) . '%</span>';
                    }
                    if ( '' !== $o['badge'] ) {
                        echo '<span class="yc-offer__badge">' . esc_html( $o['badge'] ) . '</span>';
                    }
                echo '</div>';

                echo '<div class="yc-offer__body">';
                    if ( '' !== $o['cat'] ) {
                        echo '<span class="yc-offer__cat">' . esc_html( $o['cat'] ) . '</span>';
                    }
                    echo '<h2>' . esc_html( $o['title'] ) . '</h2>';

                    echo '<div class="yc-offer__price">';
                        echo '<b>' . esc_html( yc_pt_num( $new ) ) . '</b><span>' . esc_html( $cur ) . '</span>';
                        if ( $old ) {
                            echo '<del aria-label="السعر قبل الخصم">' . esc_html( yc_pt_num( $old ) . ' ' . $cur ) . '</del>';
                        }
                    echo '</div>';

                    $features = function_exists( 'yc_author_lines' ) ? yc_author_lines( $o['features'] ) : array_filter( array_map( 'trim', explode( "\n", $o['features'] ) ) );
                    if ( $features ) {
                        echo '<ul class="yc-offer__feats">';
                        foreach ( $features as $f ) {
                            echo '<li>' . yc_pt_icon( 'check' ) . '<span>' . esc_html( $f ) . '</span></li>';
                        }
                        echo '</ul>';
                    }

                    if ( null !== $days ) {
                        if ( $days < 0 ) {
                            echo '<p class="yc-offer__exp is-over">انتهى العرض</p>';
                        } else {
                            $left = 0 === $days ? 'ينتهي اليوم' : ( 1 === $days ? 'متبقٍ يوم واحد' : ( 2 === $days ? 'متبقٍ يومان' : 'متبقٍ ' . $days . ( $days <= 10 ? ' أيام' : ' يومًا' ) ) );
                            echo '<p class="yc-offer__exp">' . yc_pt_icon( 'clip' ) . 'ينتهي في ' . esc_html( date_i18n( 'j F Y', strtotime( $o['expires'] ) ) ) . ' · <b>' . esc_html( $left ) . '</b></p>';
                        }
                    }

                    echo '<div class="yc-offer__cta">';
                        if ( $wa ) {
                            $msg = 'مرحبًا، أرغب في حجز عرض: ' . $o['title'] . ' بسعر ' . yc_pt_num( $new ) . ' ' . $cur . "\n" . get_permalink( $post );
                            echo '<a class="yc-pt-btn yc-pt-btn--wa" href="https://wa.me/' . esc_attr( $wa ) . '?text=' . rawurlencode( $msg ) . '" target="_blank" rel="noopener">' . yc_pt_wa_svg() . 'احجز العرض</a>';
                        }
                        if ( $phone ) {
                            echo '<a class="yc-pt-btn yc-pt-btn--call" href="tel:' . esc_attr( $phone ) . '" aria-label="اتصل بنا">' . yc_pt_call_svg() . '<span>اتصل</span></a>';
                        }
                    echo '</div>';
                echo '</div>';

            echo '</article>';
        }
        echo '</div>';

        /* البيانات المنظّمة: قائمة عروض */
        $list = array();
        foreach ( $offers as $k => $o ) {
            $offer = array(
                '@type'         => 'Offer',
                'name'          => $o['title'],
                'price'         => (float) $o['new'],
                'priceCurrency' => 'SAR',
                'availability'  => 'https://schema.org/InStock',
                'url'           => get_permalink( $post ),
                'itemOffered'   => array( '@type' => 'Service', 'name' => '' !== $o['cat'] ? $o['cat'] : $o['title'] ),
            );
            if ( ! empty( $o['expires'] ) ) {
                $offer['priceValidUntil'] = $o['expires'];
            }
            $list[] = array( '@type' => 'ListItem', 'position' => $k + 1, 'item' => $offer );
        }
        echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'ItemList', 'name' => get_the_title( $post ), 'itemListElement' => $list ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . '</script>';
    }

echo '</div>';
echo '</section>';
