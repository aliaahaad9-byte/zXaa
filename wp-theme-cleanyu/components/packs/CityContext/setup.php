<?php
/**
 * مدينة الصفحة الحالية — تُستخدم لعرض عنوان فرع المدينة في الفوتر.
 *
 * ترتيب التحديد:
 * 1) أرشيف مدينة (تصنيف country).
 * 2) مقال أو صفحة مرتبطة بمدينة.
 * 3) اسم مدينة موجود في عنوان الصفحة / القسم، مثل «شركة مكافحة حشرات بجدة».
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function yc_city_context() {
    static $done = false, $city = null;
    if ( $done ) {
        return $city;
    }
    $done = true;

    if ( is_tax( 'country' ) ) {
        $obj = get_queried_object();
        if ( $obj instanceof WP_Term ) {
            return $city = $obj;
        }
    }

    $title = '';
    if ( is_singular() ) {
        $id    = get_queried_object_id();
        $terms = get_the_terms( $id, 'country' );
        if ( is_array( $terms ) && $terms ) {
            return $city = $terms[0];
        }
        $title = get_the_title( $id );
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $obj   = get_queried_object();
        $title = ( $obj instanceof WP_Term ) ? $obj->name : '';
    }

    if ( '' !== trim( (string) $title ) ) {
        $city = yc_city_from_text( $title );
    }
    return $city;
}

/** يبحث عن اسم مدينة داخل نص، مع السماح بسوابق مثل «بـ» و«لـ» و«و». */
function yc_city_from_text( $text ) {
    $terms = get_terms( array( 'taxonomy' => 'country', 'hide_empty' => false ) );
    if ( is_wp_error( $terms ) || ! $terms ) {
        return null;
    }
    // الأسماء الأطول أولًا حتى لا تسبق «الخبر» «الخبر الشمالية» مثلًا
    usort( $terms, function ( $a, $b ) {
        return mb_strlen( $b->name ) - mb_strlen( $a->name );
    } );
    foreach ( $terms as $t ) {
        $name = trim( $t->name );
        if ( '' === $name ) {
            continue;
        }
        $re = '/(?:^|[^\p{L}])[وفبلك]?' . preg_quote( $name, '/' ) . '(?![\p{L}])/u';
        if ( preg_match( $re, $text ) ) {
            return $t;
        }
    }
    return null;
}

/** عنوان فرع المدينة الحالية (أو فارغ). */
function yc_city_branch_address() {
    $city = yc_city_context();
    if ( ! $city ) {
        return array( 'address' => '', 'map' => '', 'city' => null );
    }
    return array(
        'address' => trim( (string) get_term_meta( $city->term_id, 'branch_address', true ) ),
        'map'     => trim( (string) get_term_meta( $city->term_id, 'branch_map', true ) ),
        'city'    => $city,
    );
}
