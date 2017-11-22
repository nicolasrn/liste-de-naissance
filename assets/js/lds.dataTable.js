lds = (function (l) {
  let lds = l || {};

  const dupliquerEnteteTableau = function (selector) {
    $(selector + ' tfoot th').each(function () {
      var title = $(this).text();
      $(this).html('<div contenteditable data-label="' + title + '">' + title + '</div>');
      $(this).find('div').on('click', function (event) {
        if ($(this).text() === $(this).attr('data-label')) {
          $(this).empty();
        }
        $(this).focus();
      }).on('focusout', function (event) {
        if ($(this).text() === "") {
          $(this).text($(this).attr('data-label'));
        }
      });
    });
  };

  const initialiserEvenementDeFiltre = function (selector) {
    $(selector).DataTable().table().columns().every(function () {
      let that = this;
      $('div', this.footer()).on('keyup change', function () {
        if (that.search() !== this.textContent) {
          that.search(this.textContent).draw();
        }
      });
    });
  };

  const initTableFooter = function (selector) {
    dupliquerEnteteTableau(selector);
    initialiserEvenementDeFiltre(selector);
  };

  const searchOnColumn = function (selector, item) {
    let that = $(selector).DataTable().table().column(lds.config.datatable.searchColonne + ':contains(' + item.attr('placeholder') + ')');
    if (that.search() !== item.val()) {
      that.search(item.val()).draw();
    }
  };

  const initSearchForm = function (selector) {
    let formLocation = $(lds.config.datatable.defautFormLocation);
    let form = $('<form class="form-horizontal" action="#"></form>');
    $(selector + ' ' + lds.config.datatable.searchColonne).each(function () {
      let columnLabel = $(this).text();
      let formGroup = $('<div class="form-group"></div>');
      let label = $('<label class="control-label col-sm-2" for="id-' + columnLabel + '"></label>').text(columnLabel);
      let input = $('<div class="col-sm-10"><input placeholder="' + columnLabel + '" type="search" href="#' + columnLabel + '" class="form-control" id="id-' + columnLabel + '"/></div>');
      formGroup.append(label);
      formGroup.append(input);
      form.append(formGroup);
      formLocation.append(form);
    });
    let sel = selector;
    $('input', lds.config.datatable.defautFormLocation).on('keyup change', function () {
      searchOnColumn(sel, $(this));
    });
  };

  const initDataTable = function (selector) {
    $(selector).dataTable({
      paging: false
    });
    
    if (!lds.config.datatable.searching) {
      $('.dataTables_filter').hide();
    }

    if (lds.config.datatable.searchOnColonne === true) {
      switch (lds.config.datatable.locationType) {
        case 'table':
          initTableFooter(selector);
          break;
        case 'form':
          initSearchForm(selector);
          break;
        case 'both':
          initSearchForm(selector);
          initTableFooter(selector);
          break;
      }
    }
  };

  const dataTable = function (selector) {
    initDataTable(selector);
  };

  l.dataTable = dataTable;

  return lds;
})(lds);