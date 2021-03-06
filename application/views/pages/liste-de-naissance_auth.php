<div class="<?php echo $type; ?>" id="<?php echo $personne; ?>" data-url="<?php echo site_url('/contenuPage') ;?>" data-plugin="messageParZone">
</div>

<section id="liste-mes-actions">
  <script id="mes-actions-template" type="text/x-handlebars-template">
    <div id="mes-actions" class="jumbotron body" data-on="#liste-de-naissance">
      <form class="form-horizontal">
        <div class="form-group">
          <label for="search" class="col-md-2 control-label">filtrer</label>
          <div class="col-md-10">
            <input type="text" class="form-control" id="search" placeholder="libelle de l'article" value="{{search}}">
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-offset-2 col-md-10">
            <button class="btn btn-default" id="mes-articles">mettre en évidence mes réservations</button>
            <button class="btn btn-default" id="tous-les-articles">tous les articles</button>
          </div>
        </div>
      </form>
    </div>
  </script>
</section>

<section id="liste" data-url="<?php echo $urlWebService; ?>" data-etat="<?php echo $etat ?>" data-mes-actions='#mes-actions-template'>
  <script id="liste-template" type="text/x-handlebars-template">
    <div id="liste-de-naissance">
      {{#each cadeaux}}
      {{#ifmod @index @root.nbColonneAffichees 0 0}}
      <div class="row">
      {{/ifmod}}
        <div class="col-md-{{math @root.nbColonneMax '/' @root.nbColonneAffichees}}">
          <div id="article-{{this.id}}" class="thumbnail">
            <div id="carousel-{{this.id}}" class="carousel slide" data-ride="carousel" data-interval="false">
              <ol class="carousel-indicators">
                {{#eachWithParent this.img this.id}}
                  <li data-target="#carousel-{{@parentId}}" data-slide-to="{{@index}}" class="{{#eq @index 0}}active{{/eq}}"></li>
                {{/eachWithParent}}
              </ol>
              
              <div class="carousel-inner">
                {{#each this.img}}
                <div class="item {{#eq @index 0}}active{{/eq}}">
                  <img src="<?php echo base_url(); ?>{{this.src}}" class="img-responsive center-block">
                  <div class="carousel-caption">
                  </div>
                </div>
                {{/each}}	
              </div>

              <a class="left carousel-control" href="#carousel-{{this.id}}" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="right carousel-control" href="#carousel-{{this.id}}" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            </div>

            <div class="caption">
              <h2>
                {{#if this.libelleEdit}}
                  {{{this.libelleEdit}}}
                {{else}}
                  {{this.libelle}}
                {{/if}}
              </h2>
              <div class="description">
                {{#ifCond this.lieu '||' this.url}}
                <h3 class="small">
                  Vous pouvez trouver cet article
                  {{#if this.lieu}} chez {{this.lieu}} {{/if}}
                  {{#if this.url}} {{#if this.lieu}} ou {{/if}} sur  <a href="{{this.url}}" target="_blank">{{#if this.libelleUrl}}{{this.libelleUrl}}{{else}}{{this.url}}{{/if}}</a> {{/if}}
                </h3>
                {{/ifCond}}
                {{#if this.ordrePrix}}
                  <h4 class="small">Pour une valeur approximative de {{this.ordrePrix}} €</h4>
                {{/if}}
              </div>
              <div class="compteur" id="compteur-{{this.id}}">
                <div><span class="quantiteSouhaitee">{{this.quantiteSouhaitee}}</span> ça serait bien</div>
                <div>déjà <span class="quantiteReservee">{{this.quantiteReserveeTotale}}</span> réserve(s)</div>
                <div class="form-horizontal">
                  <div class="form-group">
                    <label for="quantiteReserveeUtilisateur-{{this.id}}" class="control-label col-md-4">j'en réserve</label>
                    <div class="input-group col-md-7">
                      <input type="text" id="quantiteReserveeUtilisateur-{{this.id}}" name="{{this.id}}" class="form-control" value="{{this.quantiteReserveeUtilisateur}}" readonly/>
                      <div class="input-group-btn">
                        <button type="button" class="btn btn-default moins">-</button>
                        <button type="button" class="btn btn-default plus">+</button>
                      </div>
                    </div>
                    <div class="col-md-offset-1 input-group parentFeedBack col-md-10">
                      <p class="bg-info feedBack" data-value="modification prise en compte"></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      {{#ifmod @index @root.nbColonneAffichees @root.nbColonneAffichees -1}}
      </div>
      {{/ifmod}}
      {{else}}
        <p>pas de cadeaux</p>
      {{/each}}
    </div>
  </script>
</section>