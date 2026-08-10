<?php
/**
 * Monthly Report Class
 *
 * Renders a standalone, print-optimised HTML report for a single month that the
 * site owner can save as a PDF and send to a client.
 *
 * WHY BROWSER PRINT INSTEAD OF A PHP PDF LIBRARY:
 *   FPDF/TCPDF/Dompdf cannot shape Arabic text correctly without embedding a
 *   full OpenType font plus a bidi/letter-joining layer — Arabic comes out
 *   disconnected and left-to-right. Every browser already does this natively
 *   and perfectly, so we emit clean HTML with an @page print stylesheet and let
 *   the browser's "Save as PDF" do the conversion. This also keeps the plugin
 *   dependency-free and small enough for the WordPress.org repository.
 *
 * The report renders through admin-post.php so the output is a bare page with
 * no WordPress admin chrome.
 *
 * @package WP_Floating_Contact
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPFC_Report {

    private const TIMEZONE = 'Asia/Riyadh';
    private const ACTION   = 'wpfc_pdf_report';

    public function __construct() {
        add_action( 'admin_post_' . self::ACTION, array( $this, 'render' ) );
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /** Builds the nonced URL that opens the report for a given YYYY-MM month. */
    public static function get_report_url( string $month ): string {
        return wp_nonce_url(
            admin_url( 'admin-post.php?action=' . self::ACTION . '&month=' . rawurlencode( $month ) ),
            self::ACTION
        );
    }

    /**
     * Converts a Riyadh-local calendar month into the UTC range to query.
     *
     * clicked_at is stored in UTC, but the client cares about Riyadh calendar
     * days — so "August" must mean 1 Aug 00:00 Riyadh through 1 Sep 00:00
     * Riyadh, which is 31 Jul 21:00 UTC through 31 Aug 21:00 UTC.
     *
     * @return array{0:string,1:string} [start_utc, end_utc] as 'Y-m-d H:i:s'.
     */
    private function month_bounds_utc( int $year, int $month ): array {
        $local = new DateTimeZone( self::TIMEZONE );
        $utc   = new DateTimeZone( 'UTC' );

        $start = new DateTime( sprintf( '%04d-%02d-01 00:00:00', $year, $month ), $local );
        $end   = ( clone $start )->modify( '+1 month' );

        return array(
            $start->setTimezone( $utc )->format( 'Y-m-d H:i:s' ),
            $end->setTimezone( $utc )->format( 'Y-m-d H:i:s' ),
        );
    }

    /** Formats a stored UTC datetime for display in Riyadh time. */
    private function riyadh( string $utc_datetime, string $format ): string {
        return wp_date( $format, strtotime( $utc_datetime . ' UTC' ), new DateTimeZone( self::TIMEZONE ) );
    }

    /** Resolves a URL to its WordPress post title, falling back to the logged title. */
    private function page_title_for( string $url, string $stored_title ): string {
        if ( $url ) {
            $post_id = url_to_postid( $url );
            if ( $post_id ) {
                $title = get_the_title( $post_id );
                if ( $title ) {
                    return $title;
                }
            }
        }
        return $stored_title ?: $url;
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have permission to view this report.', 'wp-floating-contact' ), 403 );
        }

        check_admin_referer( self::ACTION );

        if ( ! WPFC_License::can_use() ) {
            wp_die( esc_html__( 'Your license is not active.', 'wp-floating-contact' ), 403 );
        }

        // ── Resolve the requested month, defaulting to the current one ────────
        $requested = isset( $_GET['month'] ) ? sanitize_text_field( wp_unslash( $_GET['month'] ) ) : '';
        if ( ! preg_match( '/^(\d{4})-(\d{2})$/', $requested, $m ) ) {
            $now       = new DateTime( 'now', new DateTimeZone( self::TIMEZONE ) );
            $year      = (int) $now->format( 'Y' );
            $month_num = (int) $now->format( 'n' );
        } else {
            $year      = (int) $m[1];
            $month_num = (int) $m[2];
        }

        // Guard against out-of-range values before they reach DateTime.
        if ( $month_num < 1 || $month_num > 12 || $year < 2000 || $year > 2100 ) {
            wp_die( esc_html__( 'Invalid month requested.', 'wp-floating-contact' ), 400 );
        }

        list( $start_utc, $end_utc ) = $this->month_bounds_utc( $year, $month_num );

        $logs  = WPFC_Database::get_logs_for_range( $start_utc, $end_utc );
        $stats = $this->build_stats( $logs, $year, $month_num );

        $this->output_html( $logs, $stats, $year, $month_num );
        exit;
    }

    /**
     * Aggregates the raw log rows into everything the report needs:
     * totals, a per-day series for the chart, and a ranked page list.
     */
    private function build_stats( array $logs, int $year, int $month_num ): array {
        $days_in_month = (int) ( new DateTime( sprintf( '%04d-%02d-01', $year, $month_num ) ) )->format( 't' );

        $per_day = array_fill( 1, $days_in_month, array( 'phone' => 0, 'whatsapp' => 0 ) );
        $pages   = array();
        $phone   = 0;
        $wa      = 0;

        foreach ( $logs as $log ) {
            $day  = (int) $this->riyadh( $log->clicked_at, 'j' );
            $type = ( 'phone' === $log->click_type ) ? 'phone' : 'whatsapp';

            if ( isset( $per_day[ $day ] ) ) {
                $per_day[ $day ][ $type ]++;
            }

            if ( 'phone' === $type ) {
                $phone++;
            } else {
                $wa++;
            }

            $key = $log->page_url;
            if ( ! isset( $pages[ $key ] ) ) {
                $pages[ $key ] = array(
                    'title' => $this->page_title_for( $log->page_url, $log->page_title ),
                    'url'   => $log->page_url,
                    'phone' => 0,
                    'wa'    => 0,
                    'total' => 0,
                );
            }
            $pages[ $key ][ 'phone' === $type ? 'phone' : 'wa' ]++;
            $pages[ $key ]['total']++;
        }

        // Rank pages by total clicks, busiest first.
        uasort( $pages, static fn( $a, $b ) => $b['total'] <=> $a['total'] );

        // Busiest single day, used to scale the chart bars.
        $peak = 0;
        foreach ( $per_day as $counts ) {
            $peak = max( $peak, $counts['phone'] + $counts['whatsapp'] );
        }

        $total = $phone + $wa;

        return array(
            'total'         => $total,
            'phone'         => $phone,
            'whatsapp'      => $wa,
            'per_day'       => $per_day,
            'peak'          => $peak,
            'pages'         => $pages,
            'days_in_month' => $days_in_month,
            'daily_average' => $days_in_month > 0 ? round( $total / $days_in_month, 1 ) : 0,
        );
    }

    /** Emits the complete standalone report document. */
    private function output_html( array $logs, array $stats, int $year, int $month_num ): void {
        $is_rtl      = is_rtl();
        $month_label = wp_date(
            'F Y',
            ( new DateTime( sprintf( '%04d-%02d-01 12:00:00', $year, $month_num ), new DateTimeZone( self::TIMEZONE ) ) )->getTimestamp(),
            new DateTimeZone( self::TIMEZONE )
        );
        $generated = wp_date( 'Y-m-d g:i A', time(), new DateTimeZone( self::TIMEZONE ) );

        nocache_headers();
        header( 'Content-Type: text/html; charset=utf-8' );
        ?>
<!DOCTYPE html>
<html lang="<?php echo esc_attr( get_bloginfo( 'language' ) ); ?>" dir="<?php echo $is_rtl ? 'rtl' : 'ltr'; ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html( sprintf( __( 'Contact Report — %s', 'wp-floating-contact' ), $month_label ) ); ?></title>
<style>
    @page { size: A4; margin: 14mm; }

    * { box-sizing: border-box; }

    body {
        margin: 0;
        padding: 32px 24px 60px;
        background: #eef1f5;
        color: #1d2327;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Tahoma', Arial, sans-serif;
        font-size: 14px;
        line-height: 1.6;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .sheet {
        max-width: 900px;
        margin: 0 auto;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 6px 28px rgba(0,0,0,.10);
        overflow: hidden;
    }

    /* ── Toolbar (screen only) ── */
    .toolbar {
        max-width: 900px;
        margin: 0 auto 20px;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    .toolbar button, .toolbar a {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 11px 22px;
        border: none;
        border-radius: 9px;
        background: #1362bc;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        font-family: inherit;
        text-decoration: none;
        cursor: pointer;
    }
    .toolbar a { background: #5a6772; }
    .toolbar button:hover { background: #0f4f99; }

    /* ── Header ── */
    .head {
        background: linear-gradient(135deg, #1362bc 0%, #0b3f7d 100%);
        color: #fff;
        padding: 30px 34px;
    }
    .head h1 { margin: 0 0 6px; font-size: 25px; font-weight: 800; letter-spacing: .2px; }
    .head .site { font-size: 15px; opacity: .92; }
    .head .meta { margin-top: 14px; font-size: 12.5px; opacity: .85; }

    /* ── Sections ── */
    .section { padding: 26px 34px; border-top: 1px solid #e6eaef; page-break-inside: avoid; }
    .section:first-of-type { border-top: none; }
    .section h2 {
        margin: 0 0 16px;
        font-size: 16px;
        font-weight: 800;
        color: #0b3f7d;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section h2::before {
        content: '';
        width: 4px;
        height: 17px;
        border-radius: 3px;
        background: #1362bc;
        flex-shrink: 0;
    }

    /* ── KPI cards ── */
    .kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
    .kpi {
        background: #f7f9fc;
        border: 1px solid #e3e9f0;
        border-radius: 11px;
        padding: 16px 14px;
        text-align: center;
    }
    .kpi .n { display: block; font-size: 27px; font-weight: 800; line-height: 1.2; color: #0b3f7d; }
    .kpi .l { display: block; font-size: 12px; color: #5a6772; margin-top: 3px; }
    .kpi.phone .n { color: #1362bc; }
    .kpi.wa    .n { color: #1f9d4d; }

    /* ── Daily chart ── */
    .chart {
        display: flex;
        align-items: flex-end;
        gap: 3px;
        height: 150px;
        padding: 12px 6px 0;
        background: #f7f9fc;
        border: 1px solid #e3e9f0;
        border-radius: 11px;
    }
    .bar-wrap { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; height: 100%; }
    /* The track absorbs the leftover height so a 100% bar can never overflow
       the chart box the way it would if it sized against .bar-wrap directly. */
    .track { flex: 1; width: 100%; display: flex; align-items: flex-end; min-height: 0; }
    .bar { width: 100%; display: flex; flex-direction: column-reverse; border-radius: 3px 3px 0 0; overflow: hidden; min-height: 2px; }
    .bar .seg-phone { background: #1362bc; }
    .bar .seg-wa    { background: #2ecc5f; }
    .bar-wrap .d { font-size: 8.5px; color: #7c8791; line-height: 1; }
    .bar-wrap .v { font-size: 9px; font-weight: 700; color: #3c4650; line-height: 1; }

    .legend { display: flex; gap: 18px; margin-top: 12px; font-size: 12px; color: #5a6772; }
    .legend i { display: inline-block; width: 11px; height: 11px; border-radius: 3px; vertical-align: -1px; margin-inline-end: 6px; }

    /* ── Tables ── */
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th {
        background: #f2f5f9;
        color: #0b3f7d;
        font-weight: 700;
        text-align: start;
        padding: 10px 12px;
        border-bottom: 2px solid #dbe3ec;
        font-size: 12.5px;
    }
    td { padding: 9px 12px; border-bottom: 1px solid #edf1f6; vertical-align: top; }
    tr { page-break-inside: avoid; }
    tbody tr:nth-child(even) { background: #fafbfd; }
    .num { font-variant-numeric: tabular-nums; white-space: nowrap; }
    .url { color: #7c8791; font-size: 11px; word-break: break-all; }

    .tag {
        display: inline-block;
        padding: 2.5px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        white-space: nowrap;
    }
    .tag.phone { background: #e4effb; color: #1362bc; }
    .tag.wa    { background: #e3f7ec; color: #1f9d4d; }

    .empty { text-align: center; padding: 40px 20px; color: #7c8791; }

    .foot {
        padding: 18px 34px 26px;
        border-top: 1px solid #e6eaef;
        text-align: center;
        color: #8a949e;
        font-size: 11.5px;
    }

    /* ── Print ── */
    @media print {
        body { background: #fff; padding: 0; font-size: 11.5px; }
        .toolbar { display: none !important; }
        .sheet { box-shadow: none; border-radius: 0; max-width: none; }
        .section { padding: 16px 0; }
        .head { padding: 20px 0; border-radius: 0; }
        .foot { padding: 12px 0; }
        .log-section { page-break-before: always; }
        thead { display: table-header-group; }
    }
</style>
</head>
<body>

<div class="toolbar">
    <button type="button" onclick="window.print()">
        <?php esc_html_e( '⬇ Save as PDF', 'wp-floating-contact' ); ?>
    </button>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpfc-analytics' ) ); ?>">
        <?php esc_html_e( 'Back', 'wp-floating-contact' ); ?>
    </a>
</div>

<div class="sheet">

    <div class="head">
        <h1><?php esc_html_e( 'Monthly Contact Report', 'wp-floating-contact' ); ?></h1>
        <div class="site"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></div>
        <div class="meta">
            <?php echo esc_html( sprintf(
                /* translators: 1: month name and year, 2: generation timestamp */
                __( 'Period: %1$s  ·  Generated: %2$s  ·  Riyadh time (UTC+3)', 'wp-floating-contact' ),
                $month_label,
                $generated
            ) ); ?>
        </div>
    </div>

    <!-- ── Summary ── -->
    <div class="section">
        <h2><?php esc_html_e( 'Summary', 'wp-floating-contact' ); ?></h2>
        <div class="kpis">
            <div class="kpi">
                <span class="n"><?php echo esc_html( number_format_i18n( $stats['total'] ) ); ?></span>
                <span class="l"><?php esc_html_e( 'Total Clicks', 'wp-floating-contact' ); ?></span>
            </div>
            <div class="kpi phone">
                <span class="n"><?php echo esc_html( number_format_i18n( $stats['phone'] ) ); ?></span>
                <span class="l"><?php esc_html_e( 'Phone Calls', 'wp-floating-contact' ); ?></span>
            </div>
            <div class="kpi wa">
                <span class="n"><?php echo esc_html( number_format_i18n( $stats['whatsapp'] ) ); ?></span>
                <span class="l"><?php esc_html_e( 'WhatsApp Chats', 'wp-floating-contact' ); ?></span>
            </div>
            <div class="kpi">
                <span class="n"><?php echo esc_html( number_format_i18n( $stats['daily_average'], 1 ) ); ?></span>
                <span class="l"><?php esc_html_e( 'Daily Average', 'wp-floating-contact' ); ?></span>
            </div>
        </div>
    </div>

    <!-- ── Daily chart ── -->
    <div class="section">
        <h2><?php esc_html_e( 'Daily Activity', 'wp-floating-contact' ); ?></h2>
        <?php if ( $stats['peak'] > 0 ) : ?>
        <div class="chart">
            <?php foreach ( $stats['per_day'] as $day => $counts ) :
                $day_total = $counts['phone'] + $counts['whatsapp'];
                // Bars are scaled against the busiest day so the shape is readable.
                $height    = $stats['peak'] > 0 ? ( $day_total / $stats['peak'] ) * 100 : 0;
                $ph_share  = $day_total > 0 ? ( $counts['phone'] / $day_total ) * 100 : 0;
                ?>
            <div class="bar-wrap">
                <span class="v"><?php echo $day_total > 0 ? esc_html( $day_total ) : '&nbsp;'; ?></span>
                <div class="track">
                    <div class="bar" style="height:<?php echo esc_attr( round( $height, 2 ) ); ?>%">
                        <span class="seg-phone" style="height:<?php echo esc_attr( round( $ph_share, 2 ) ); ?>%"></span>
                        <span class="seg-wa" style="height:<?php echo esc_attr( round( 100 - $ph_share, 2 ) ); ?>%"></span>
                    </div>
                </div>
                <span class="d"><?php echo esc_html( $day ); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="legend">
            <span><i style="background:#1362bc"></i><?php esc_html_e( 'Phone', 'wp-floating-contact' ); ?></span>
            <span><i style="background:#2ecc5f"></i><?php esc_html_e( 'WhatsApp', 'wp-floating-contact' ); ?></span>
        </div>
        <?php else : ?>
        <p class="empty"><?php esc_html_e( 'No activity recorded in this month.', 'wp-floating-contact' ); ?></p>
        <?php endif; ?>
    </div>

    <!-- ── Top pages ── -->
    <?php if ( ! empty( $stats['pages'] ) ) : ?>
    <div class="section">
        <h2><?php esc_html_e( 'Top Pages', 'wp-floating-contact' ); ?></h2>
        <table>
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th><?php esc_html_e( 'Page', 'wp-floating-contact' ); ?></th>
                    <th style="width:80px;" class="num"><?php esc_html_e( 'Phone', 'wp-floating-contact' ); ?></th>
                    <th style="width:90px;" class="num"><?php esc_html_e( 'WhatsApp', 'wp-floating-contact' ); ?></th>
                    <th style="width:70px;" class="num"><?php esc_html_e( 'Total', 'wp-floating-contact' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ( array_slice( $stats['pages'], 0, 15 ) as $page ) : ?>
                <tr>
                    <td class="num"><?php echo esc_html( $i++ ); ?></td>
                    <td>
                        <strong><?php echo esc_html( $page['title'] ); ?></strong>
                        <div class="url"><?php echo esc_html( $page['url'] ); ?></div>
                    </td>
                    <td class="num"><?php echo esc_html( number_format_i18n( $page['phone'] ) ); ?></td>
                    <td class="num"><?php echo esc_html( number_format_i18n( $page['wa'] ) ); ?></td>
                    <td class="num"><strong><?php echo esc_html( number_format_i18n( $page['total'] ) ); ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- ── Full log ── -->
    <?php if ( ! empty( $logs ) ) : ?>
    <div class="section log-section">
        <h2><?php esc_html_e( 'Detailed Click Log', 'wp-floating-contact' ); ?></h2>
        <table>
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="width:110px;"><?php esc_html_e( 'Type', 'wp-floating-contact' ); ?></th>
                    <th style="width:100px;"><?php esc_html_e( 'Date', 'wp-floating-contact' ); ?></th>
                    <th style="width:85px;"><?php esc_html_e( 'Time', 'wp-floating-contact' ); ?></th>
                    <th><?php esc_html_e( 'Page', 'wp-floating-contact' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $n = 1; foreach ( $logs as $log ) : $is_phone = ( 'phone' === $log->click_type ); ?>
                <tr>
                    <td class="num"><?php echo esc_html( $n++ ); ?></td>
                    <td>
                        <span class="tag <?php echo $is_phone ? 'phone' : 'wa'; ?>">
                            <?php echo $is_phone
                                ? esc_html__( 'Phone', 'wp-floating-contact' )
                                : esc_html__( 'WhatsApp', 'wp-floating-contact' ); ?>
                        </span>
                    </td>
                    <td class="num"><?php echo esc_html( $this->riyadh( $log->clicked_at, 'Y-m-d' ) ); ?></td>
                    <td class="num"><?php echo esc_html( $this->riyadh( $log->clicked_at, 'g:i A' ) ); ?></td>
                    <td><?php echo esc_html( $this->page_title_for( $log->page_url, $log->page_title ) ); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="foot">
        <?php echo esc_html( sprintf(
            /* translators: %s: site name */
            __( 'Report generated by WP Floating Contact Buttons for %s', 'wp-floating-contact' ),
            get_bloginfo( 'name' )
        ) ); ?>
    </div>

</div>

<script>
    /* Open the print dialog once layout has settled so the PDF paginates correctly. */
    window.addEventListener( 'load', function () {
        setTimeout( function () { window.print(); }, 600 );
    } );
</script>

</body>
</html>
        <?php
    }
}
