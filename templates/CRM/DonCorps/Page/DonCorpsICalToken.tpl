{*
 * Interface de gestion de l'abonnement iCalendar Don Corps.
 *
 * Cette page permet à l'utilisateur connecté :
 * - de visualiser l'état de son abonnement iCalendar ;
 * - de générer une nouvelle URL personnelle d'abonnement ;
 * - de copier cette URL ;
 * - de révoquer un abonnement existant.
 *
 * L'URL générée contient un token personnel permettant l'accès au flux
 * iCalendar sans authentification CiviCRM. Le token lui-même n'est jamais
 * affiché ni stocké par cette page en dehors de l'URL générée.
 *
 * Les variables Smarty utilisées sont fournies par
 * CRM_DonCorps_Page_DonCorpsICalToken :
 *   - $hasActiveToken : indique si un abonnement est actuellement actif ;
 *   - $icalUrl        : contient la nouvelle URL iCalendar générée.
 *
 * Les classes CSS sont préfixées par "don-corps-ical" afin d'éviter
 * les conflits avec les styles de CiviCRM ou du thème WordPress.
 *}


 {*
 * Mise en forme autonome de l'interface.
 * Les styles sont volontairement limités à cette page et utilisent
 * le préfixe "don-corps-ical" pour éviter les collisions avec
 * les styles CiviCRM/WordPress.
 *}

<style>

<style>
.don-corps-ical {
    max-width: 950px;
    margin: 30px auto;
}

.don-corps-ical h2 {
    margin-bottom: 25px;
}

.don-corps-ical h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #245b73;
}

.don-corps-ical-box {
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 20px;
    margin-bottom: 25px;
    background: #fff;
}

.don-corps-ical-box p {
    margin-top: 0;
}

.don-corps-ical-url {
    display: flex;
    gap: 10px;
    align-items: center;
}

.don-corps-ical-url input {
    flex: 1;
    min-width: 0;
    padding: 8px 10px;
    border: 1px solid #bbb;
    border-radius: 3px;
    font-size: 13px;
}

.don-corps-ical-button {
    border: none;
    border-radius: 3px;
    padding: 8px 15px;
    cursor: pointer;
    font-weight: 600;
}

.don-corps-ical-generate {
    background: #287a9c;
    color: white;
}

.don-corps-ical-generate:hover {
    background: #1e5f7a;
}

.don-corps-ical-revoke {
    background: #b23b3b;
    color: white;
}

.don-corps-ical-revoke:hover {
    background: #922d2d;
}

.don-corps-ical-info {
    background: #edf7ed;
    border: 1px solid #c8dfc8;
    padding: 12px 15px;
    border-radius: 4px;
    margin-bottom: 15px;
}

.don-corps-ical-warning {
    background: #fff5f5;
    border: 1px solid #e0c0c0;
    padding: 12px 15px;
    border-radius: 4px;
    margin-bottom: 15px;
}

.don-corps-ical-help {
    font-size: 13px;
    color: #666;
    margin-top: 12px !important;
}
</style>

{*
 * Trois situations sont présentées :
 * 1. un abonnement existe : possibilité de le révoquer ;
 * 2. aucun abonnement n'existe : possibilité d'en créer un ;
 * 3. après génération : affichage de la nouvelle URL à copier.
 *}
 
<div class="don-corps-ical">

  {if $hasActiveToken}

    <div class="don-corps-ical-box">

      <h3>Abonnement iCalendar actif</h3>

      <div class="don-corps-ical-info">
        Vous disposez actuellement d'un abonnement iCalendar actif.
      </div>

      <form method="post">

        <button type="submit"
                class="don-corps-ical-button don-corps-ical-revoke"
                name="revoke_ical_token"
                value="1"
                onclick="return confirm('Voulez-vous vraiment révoquer votre abonnement iCalendar ?');">
          Révoquer mon abonnement
        </button>

      </form>

    </div>

  {else}

    <div class="don-corps-ical-box">

      <h3>Aucun abonnement actif</h3>

      <div class="don-corps-ical-warning">
        Vous ne disposez actuellement d'aucun abonnement iCalendar actif.
      </div>

    </div>

  {/if}


  <div class="don-corps-ical-box">

    <h3>Créer un abonnement iCalendar</h3>

    <p>
      Cliquez sur le bouton ci-dessous pour générer une adresse personnelle
      permettant de vous abonner au calendrier Don Corps.
    </p>

    <form method="post">

      <button type="submit"
              class="don-corps-ical-button don-corps-ical-generate"
              name="generate_ical_token"
              value="1">
        Générer mon URL iCalendar
      </button>

    </form>

  </div>


  {if $icalUrl}

    <div class="don-corps-ical-box">

      <h3>Votre adresse iCalendar</h3>

      <div class="don-corps-ical-info">
        Votre nouvelle adresse d'abonnement a été générée.
      </div>

      <div class="don-corps-ical-url">

        <input
          type="text"
          value="{$icalUrl|escape}"
          readonly
          id="don-corps-ical-url"
        />

        <button
        {*
        * Copie l'URL dans le presse-papiers sans exposer de traitement
        * supplémentaire côté serveur.
        *}
          type="button"
          class="don-corps-ical-button don-corps-ical-generate"
          onclick="navigator.clipboard.writeText(
            document.getElementById('don-corps-ical-url').value
          );">
          Copier
        </button>

      </div>

      <p class="don-corps-ical-help">
        Utilisez cette adresse dans Apple Calendar ou dans une autre
        application compatible avec les abonnements iCalendar.
      </p>

    </div>

  {/if}

</div>