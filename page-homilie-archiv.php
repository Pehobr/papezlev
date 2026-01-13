<?php
/*
Template Name: Archiv Homilií (Filtr)
*/

// Získání parametrů z URL nebo defaultní hodnoty
$current_year = date('Y');
$current_month = date('n');

$selected_year = isset($_GET['h_year']) ? intval($_GET['h_year']) : $current_year;
$selected_month = isset($_GET['h_month']) ? intval($_GET['h_month']) : $current_month;

// Názvy měsíců
$months = array(
    1 => 'Leden', 2 => 'Únor', 3 => 'Březen', 4 => 'Duben',
    5 => 'Květen', 6 => 'Červen', 7 => 'Červenec', 8 => 'Srpen',
    9 => 'Září', 10 => 'Říjen', 11 => 'Listopad', 12 => 'Prosinec'
);

// --- LOGIKA PRO EXPORT DO WORDU ---
if ( isset($_GET['export']) && $_GET['export'] === 'word' ) {
    
    // Mapa měsíců bez diakritiky pro název souboru
    $months_ascii = array(
        1 => 'Leden', 2 => 'Unor', 3 => 'Brezen', 4 => 'Duben',
        5 => 'Kveten', 6 => 'Cerven', 7 => 'Cervenec', 8 => 'Srpen',
        9 => 'Zari', 10 => 'Rijen', 11 => 'Listopad', 12 => 'Prosinec'
    );
    
    // Název souboru (bezpečný)
    $filename = 'Homilie_' . $months_ascii[$selected_month] . '_' . $selected_year . '.doc';
    
    // Hlavičky pro stažení souboru
    // Pozn: charset=UTF-8 je důležité pro správné zobrazení češtiny uvnitř
    header("Content-Type: application/vnd.ms-word; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: 0"); 
 
    
    ?>
    <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
    <head>
        <meta charset="utf-8">
        <title><?php echo 'Homilie ' . $months[$selected_month] . ' ' . $selected_year; ?></title>
        <style>
            /* Nastavení stránky pro Word: A4, okraje 1cm */
            @page {
                size: 21cm 29.7cm;
                margin: 1cm;
                mso-page-orientation: portrait;
            }
            body {
                font-family: "Times New Roman", serif;
                font-size: 12pt;
                line-height: 1.2;
            }
            h1 {
                font-size: 24pt;
                text-align: center;
                margin-bottom: 20pt;
                color: #000;
            }
            h2 {
                font-size: 16pt;
                margin-top: 20pt;
                margin-bottom: 10pt;
                color: #870e2c !important; /* Čeervená nadpisu */
                border-bottom: 1px solid #ccc;
                padding-bottom: 5px;
            }
            p {
                margin: 0 0 10pt 0;
                text-align: justify;
            }
            .meta {
                color: #555;
                font-size: 10pt;
                margin-bottom: 15pt;
                font-style: italic;
            }
            a {
                color: #870e2c;
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <h1>Homilie - <?php echo $months[$selected_month] . ' ' . $selected_year; ?></h1>
        
        <?php
        // Stejný dotaz jako pro web
        $args = array(
            'category_name'  => 'homilie',
            'posts_per_page' => -1,
            'date_query'     => array(
                array(
                    'year'  => $selected_year,
                    'month' => $selected_month,
                ),
            ),
            'orderby'        => 'date',
            'order'          => 'ASC',
        );
        
        $export_query = new WP_Query( $args );
        
        if ( $export_query->have_posts() ) {
            while ( $export_query->have_posts() ) {
                $export_query->the_post();
                ?>
                <div class="entry">
                    <h2><?php the_title(); ?></h2>
                    <div class="meta">
                        Datum: <?php echo get_the_date(); ?><br>
                        Zdroj: <?php the_permalink(); ?>
                    </div>
                    <div class="content">
                        <?php 
                        global $post;
                        // Použijeme apply_filters na content, aby se provedly shortcody, ale ořízneme složité věci
                        $content = apply_filters('the_content', $post->post_content);
                        echo $content;
                        ?>
                    </div>
                    <br style="page-break-after: always; mso-special-character:line-break;">
                    <hr>
                </div>
                <?php
            }
        } else {
            echo '<p>V tomto období nebyly nalezeny žádné homilie.</p>';
        }
        wp_reset_postdata();
        ?>
    </body>
    </html>
    <?php
    // Ukončení skriptu po exportu
    exit;
}

// --- KONEC LOGIKY PRO EXPORT, ZAČÁTEK BĚŽNÉ STRÁNKY ---

get_header(); 
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <header class="page-header">
            <h1 class="page-title"><?php the_title(); ?></h1>
        </header>

        <!-- Sekce filtru a ovládání -->
        <section class="homilie-filter-section" style="margin-bottom: 40px; background: #f9f9f9; padding: 20px; border-radius: 5px;">
            <form method="get" action="<?php echo esc_url( get_permalink() ); ?>" style="display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;">
                
                <div class="form-group">
                    <label for="h_month" style="display: block; margin-bottom: 5px; font-weight: bold;">Měsíc:</label>
                    <select name="h_month" id="h_month" style="padding: 5px; min-width: 120px;">
                        <?php foreach ($months as $num => $name) : ?>
                            <option value="<?php echo $num; ?>" <?php selected( $selected_month, $num ); ?>>
                                <?php echo $name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="h_year" style="display: block; margin-bottom: 5px; font-weight: bold;">Rok:</label>
                    <select name="h_year" id="h_year" style="padding: 5px; min-width: 80px;">
                        <?php 
                        for ($y = $current_year; $y >= 2013; $y--) : ?>
                            <option value="<?php echo $y; ?>" <?php selected( $selected_year, $y ); ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Tlačítko Zobrazit (jen pro web) -->
                <button type="submit" style="padding: 7px 20px; cursor: pointer; background-color: #444; color: white; border: none; border-radius: 3px; font-weight: bold;">
                    <i class="fa fa-filter"></i> Zobrazit
                </button>

                <!-- Tlačítko Export do Wordu -->
                <!-- Použijeme name="export" value="word", aby se po odeslání spustil export -->
                <button type="submit" name="export" value="word" style="padding: 7px 20px; cursor: pointer; background-color: #1a5c96; color: white; border: none; border-radius: 3px; font-weight: bold; margin-left: auto;">
                    <i class="fa fa-file-word-o"></i> Export do Wordu (A4)
                </button>
            </form>
        </section>

        <?php
        $args = array(
            'category_name'  => 'homilie', 
            'posts_per_page' => -1,        
            'date_query'     => array(
                array(
                    'year'  => $selected_year,
                    'month' => $selected_month,
                ),
            ),
            'orderby'        => 'date',
            'order'          => 'ASC',
        );

        $homilie_query = new WP_Query( $args );

        if ( $homilie_query->have_posts() ) :
            ?>
            <div class="homilie-results">
                <h3 style="margin-bottom: 30px; border-bottom: 2px solid #870e2c; display: inline-block; padding-bottom: 5px;">
                    Nalezené homilie: <?php echo $months[$selected_month] . ' ' . $selected_year; ?>
                </h3>
            
                <?php
                while ( $homilie_query->have_posts() ) : $homilie_query->the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'homilie-entry' ); ?> style="margin-bottom: 50px; border-bottom: 1px solid #eee; padding-bottom: 30px;">
                        <header class="entry-header">
                            <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
                            
                            <div class="entry-meta" style="margin-bottom: 15px; color: #666; font-size: 0.9em;">
                                <span class="posted-on">
                                    <i class="fa fa-calendar" aria-hidden="true"></i> <?php echo get_the_date(); ?>
                                </span>
                            </div>
                        </header>

                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </article>
                    <?php
                endwhile;
                ?>
            </div>
            <?php
            wp_reset_postdata();

        else :
            ?>
            <div class="no-results">
                <p>Pro vybrané období (<?php echo $months[$selected_month] . ' ' . $selected_year; ?>) nebyly nalezeny žádné homilie.</p>
            </div>
            <?php
        endif;
        ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
