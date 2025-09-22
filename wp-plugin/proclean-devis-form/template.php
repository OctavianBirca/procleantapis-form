<?php /* Simplified include of your existing index markup pieces */ ?>
<?php $plugin_base = plugin_dir_url(__FILE__); ?>
<div class="proclean-form">
        <style>
            /* Fallback to ensure tabs are visible even if theme CSS conflicts */
            .proclean-form .tab-content{display:none}
            .proclean-form .tab-content.active{display:block}
        </style>
    <div class="container">
    <h1>Demande de Devis</h1>
            <?php
                $base_dir = function_exists('plugin_dir_path') ? plugin_dir_path(__FILE__) : __DIR__ . '/';
                $partials = [
                'tabs.html', 'tapis.html', 'textile.html', 'literie.html', 'pro.html', 'contact.html'
            ];
            foreach ($partials as $p) {
                    $path = rtrim($base_dir, '/\\') . '/partials/' . $p;
                if (file_exists($path)) {
                        include $path;
                } else {
                        if (function_exists('error_log')) { error_log('[PCDF] Missing partial: ' . $path); }
                        echo '<!-- PCDF: missing ' . esc_html($p) . ' -->';
                }
            }
        ?>
    </div>
</div>
