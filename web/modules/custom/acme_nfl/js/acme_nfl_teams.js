(function ($, Drupal) {

  const listsContainerSelector = '#acme-nfl-teams-list';

  Drupal.behaviors.acmeNflTeams = {
    attach: function (context, settings) {
      $(context).once('acmeNflTeams').find(listsContainerSelector).each(() => {
        const lists = [];
        const search = {
          'fullname': '',
          'division': '',
        };

        // Initialize lists
        $(`${listsContainerSelector} .list-wrapper`).each((_, listEl) => {
          const list = new List(listEl, {
            valueNames: ['fullname', 'division']
          });

          lists.push(list);
          window.lists = lists;
        });

        //
        $('#acme-nfl-teams-name-search').on('keyup', (event) => {
          search.fullname = event.target.value || '';
          updateSearch();
        });

        //
        $('#acme-nfl-teams-division-select').on('change', (event) => {
            search.division = event.target.value || '';
            updateSearch();
        });

        /**
         * Apply search terms to the list
         */
        const updateSearch = () => {
          lists.forEach(list => {
            const columns = Object.keys(search);
            const searchStr = Object
              .values(search)
              .map(term => term.length > 0 ? `"${term}"` : '')
              .join(' ')
              .trim();

            list.search(searchStr, columns);
            // Object.entries(search).forEach(([column, term]) => {
            //     list.search(term, [column]);
            // });
            //
            // if (term && term.length > 0) {
            //   list.search(term, [column]);
            // } else {
            //   list.search('', [column]);
            // }
          });
        };
      });
    }
  };

}(jQuery, Drupal));
