<?php
/**
 * Template pentru formularul de covoare
 * Poate fi inclus în orice pagină WordPress folosind shortcode [carpet_form]
 */
?>

<div class="container">
    <div class="tab-navigation">
        <button class="tab-button active" data-tab="tapis">Tapis</button>
        <button class="tab-button" data-tab="textile">Textile</button>
        <button class="tab-button" data-tab="literie">Literie</button>
        <button class="tab-button" data-tab="professionnel">Professionnel</button>
    </div>

    <form id="carpet-form" method="post">
        <!-- Tab Tapis -->
        <div class="tab-content active" id="tapis">
            <!-- Copiați conținutul din index.html pentru tab-ul tapis -->
            <h2>Nettoyage de Tapis</h2>
            <!-- ... restul conținutului ... -->
        </div>

        <!-- Tab Textile -->
        <div class="tab-content" id="textile">
            <!-- Copiați conținutul din index.html pentru tab-ul textile -->
            <h2>Nettoyage de Textile</h2>
            <!-- ... restul conținutului ... -->
        </div>

        <!-- Tab Literie -->
        <div class="tab-content" id="literie">
            <!-- Copiați conținutul din index.html pentru tab-ul literie -->
            <h2>Nettoyage de Literie</h2>
            <!-- ... restul conținutului ... -->
        </div>

        <!-- Tab Professionnel -->
        <div class="tab-content" id="professionnel">
            <!-- Copiați conținutul din index.html pentru tab-ul professionnel -->
            <h2>Devis pour les professionnels</h2>
            <!-- ... restul conținutului ... -->
        </div>

        <!-- Formular de contact -->
        <div class="contact-data-section">
            <h2>Données de Contact</h2>
            
            <div class="contact-form-grid">
                <div class="contact-row">
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" id="nom" name="nom" placeholder="Saisir votre nom" required>
                    </div>
                    <div class="form-group">
                        <label for="courriel">Courriel</label>
                        <input type="email" id="courriel" name="courriel" placeholder="Saisir une adresse mail valide" required>
                    </div>
                </div>

                <div class="contact-row">
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" placeholder="Saisir votre téléphone">
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" placeholder="JJ/MM/AAAA">
                    </div>
                </div>

                <div class="contact-row">
                    <div class="form-group">
                        <label for="rue">Rue</label>
                        <input type="text" id="rue" name="rue" placeholder="Saisir votre rue">
                    </div>
                </div>

                <div class="contact-row">
                    <div class="form-group">
                        <label for="ville">Ville</label>
                        <input type="text" id="ville" name="ville" placeholder="Ville">
                    </div>
                    <div class="form-group">
                        <label for="code-postal">Code Postal</label>
                        <input type="text" id="code-postal" name="code-postal" placeholder="Code Postal">
                    </div>
                </div>

                <div class="contact-row">
                    <div class="form-group full-width">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="4" placeholder="Votre message..."></textarea>
                    </div>
                </div>
            </div>

            <div class="submit-section">
                <?php wp_nonce_field('carpet_form_nonce', 'carpet_nonce'); ?>
                <button type="submit" class="submit-btn">Envoyer la demande</button>
            </div>
        </div>
    </form>
</div>