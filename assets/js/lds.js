var lds = (function (l) {
  const clonerEtSupprimer = function (element) {
    let newElt = element.cloneNode(true);
    supprimer(element);
    return newElt;
  }

  const supprimer = function (element) {
    element.parentNode.removeChild(element);
  }

  const onChange = function (event) {
    let form = event.target.parentNode.parentNode.parentNode;

    event.preventDefault();
    $("#testform").serialize()
    $.ajax(form.action, {
      'method': form.method,
      'data': $(form).serialize()
    }).success(function (data) {}).error(function () {});
  }

  function CsvToCheckBox(idTemplate, dest) {
    let self = this;
    self.template = clonerEtSupprimer(document.querySelector(idTemplate));
    self.destinations = document.querySelectorAll(dest);

    let ids = self.template.getAttribute('data-id').split(';');
    let libelles = self.template.getAttribute('data-libelle').split(';');

    self.templateCheckbox = clonerEtSupprimer(self.template.querySelector('.checkbox'));

    ids.forEach(function (item, index) {
      if (item !== "") {
        let checkbox = self.templateCheckbox.cloneNode(true);
        let input = checkbox.querySelector('input');
        let label = checkbox.querySelector('label');

        input.id = "input-" + item;
        input.name = "inputHabilitation[]";
        input.value = item;

        label.setAttribute('for', input.id);
        label.appendChild(document.createTextNode(libelles[index]));

        self.template.appendChild(checkbox);
      }
    });

    for (let indexDestination = 0; indexDestination < self.destinations.length; indexDestination++) {
      let destination = self.destinations[indexDestination];
      let idPersonne = destination.parentNode.children[0].textContent;
      let groupesSelectionnes = destination.textContent.split(';');
      let form = self.template.cloneNode(true);

      destination.textContent = "";

      form.querySelector('input[type=hidden]').value = idPersonne;
      let inputs = form.querySelectorAll('input[type=checkbox]');
      let labels = form.querySelectorAll('label');
      for (let index = 0; index < inputs.length; index++) {
        let input = inputs[index];
        input.id = indexDestination + '-' + input.id;
        labels[index].setAttribute('for', input.id);
        input.addEventListener('change', onChange, false);
        groupesSelectionnes.forEach(function (item, index) {
          if (input.value === item) {
            input.checked = true;
          }
        });
      }

      destination.appendChild(form);
    }
  }

  const toCheckBox = function (idTemplate, dest) {
    return new CsvToCheckBox(idTemplate, dest);
  }

  l = l || {};
  l.toCheckBox = toCheckBox;

  return l;
})(lds);