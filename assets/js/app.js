;
(function ($, lds) {
  'use-strict';

  $('[data-connexion-rapide]').connexionRapide();
  $('[data-show-message]').showMessage();
  $('#menuAdmin').menu({
    url: '/index.php/home/menu'
  });
  $('#ajouterImage').addFileToForm();
  $('[data-plugin=messageParZone]').messageParZone();

  lds.dataTable('[data-plugin=dataTable]');

  if (Cookies.get('utilisateur')) {
    $('section#liste').liste({
      'user': JSON.parse(Cookies.get('utilisateur')),
      'templateMesActions': '#mes-actions-template'
    });
    $('.compteur').compteur({
      'idUser': JSON.parse(Cookies.get('utilisateur')),
      'isEditMode': true
    });
  }
  
})(jQuery, lds);